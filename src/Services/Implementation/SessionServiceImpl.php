<?php

namespace Powon\Services\Implementation;

use Powon\Dao\MemberDAO;
use Powon\Dao\SessionDAO;
use Powon\Entity\Member;
use Powon\Entity\Session;
use Powon\Services\SessionService;
use Powon\Utils\Token;
use Psr\Log\LoggerInterface;

/**
 * Class SessionServiceImpl
 * The basic implementation of the session service
 * @package Powon\Services\Implementation
 */
class SessionServiceImpl implements SessionService
{

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var MemberDAO
     */
    protected $memberDAO;

    /**
     * @var SessionDAO
     */
    protected $sessionDAO;

    /**
     * The session entity corresponding with the current session
     * @var Session
     */
    private $session;

    /**
     * Logged in member.
     * @var Member
     */
    private $member;

    /**
     * Expiration in seconds.
     * @var int
     */
    private $expiration = 24*3600;

    /**
     * Default key for generating tokens.
     * @var string
     */
    private $key = 'xbP#uj$anK';

    /**
     * Indicates if the session state.
     * SESSION_ACTIVE, SESSION_EXPIRED, SESSION_LOGOUT, SESSION_DOES_NOT_EXIST
     * @var int
     */
    private $sessionState = SessionService::SESSION_DOES_NOT_EXIST;
    
    /**
     * SessionServiceImpl constructor.
     * @param LoggerInterface $log
     * @param MemberDAO $memberDAO
     * @param SessionDAO $sessionDAO
     */
    public function __construct(LoggerInterface $log, MemberDAO $memberDAO, SessionDAO $sessionDAO)
    {
        $this->log = $log;
        $this->memberDAO = $memberDAO;
        $this->sessionDAO = $sessionDAO;
    }

    /**
     * Loads a session for the current request.
     * @param string $token An authentication token as generated by the @link \Powon\Utils\Token.
     * @return int (SESSION_ACTIVE, SESSION_EXPIRED, SESSION_DOES_NOT_EXIST)
     */
    public function loadSession($token)
    {
        try {
            $this->session = $this->sessionDAO->getSession($token);
        } catch (\PDOException $ex) {
            $this->log->error("PDO error: $ex->getMessage()", ['code' => $ex->getCode()]);
        }
        if ($this->session && time() >= ($this->session->getLastAccess() + $this->expiration)) {
            $this->log->info("Session with token expired.", array('token' => $token));
            try {
                $this->sessionDAO->deleteSession($token);
            } catch (\PDOException $ex) {
                // not much we can do..
                $this->log->warning("Session could not be deleted. $ex->getMessage()");
            }
            $this->session = null;
            $this->member = null;
            $this->sessionState = SessionService::SESSION_EXPIRED;
        } else if ($this->session) {
            // load member
            $member_id = $this->session->getMemberId();
            $this->log->debug('Loaded session for member with id.', array('token' => $token, 'member' => $member_id));
            $this->log->debug("Session data:", $this->session->getSessionData());
            try {
                $this->member = $this->memberDAO->getMemberById($member_id);
                // update session's last_access.
                $this->session->setLastAccess(time());
                $this->sessionDAO->updateSession($this->session);
                $this->sessionState = SessionService::SESSION_ACTIVE;
            } catch (\PDOException $ex) {
                $this->log->error("PDO exception was thrown. $ex->getMessage()");
                $this->member = null;
                $this->session = null;
                $this->sessionState = SessionService::SESSION_DOES_NOT_EXIST;
            }
        } else {
            $this->log->debug("Session with token $token does not exist.");
            $this->sessionState =  SessionService::SESSION_DOES_NOT_EXIST;
        }
        return $this->sessionState;
    }

    /**
     * Called after authentication. It generates a new session for the given user.
     * @param bool $remember Set to true if you want session to persist accross browser sessions.
     * @return int the session state
     */
    private function generateSessionForMember($remember) {
        assert($this->member != null);
        $member_id = $this->member->getMemberId();
        $data['token'] = Token::generate($this->key);
        $this->log->debug('Generated token for member.', array('member_id' => $member_id, 'token' => $data['token']));
        $data['session_data'] = [
            'remember' => $remember
        ];
        $data['member_id'] = $member_id;
        $data['last_access'] = time();
        $this->session = new Session($data);
        try {
            if ($this->sessionDAO->createSession($this->session)) {
                $this->sessionState = SessionService::SESSION_ACTIVE;
            } else {
                $this->sessionState = SessionService::SESSION_DOES_NOT_EXIST;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A PDO exception occurred. $ex->getMessage()");
            $this->sessionState = SessionService::SESSION_DOES_NOT_EXIST;
        }
        return $this->sessionState;
    }

    /**
     * Authenticates a user by username.
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @return bool true on success, false otherwise
     */
    public function authenticateUserByUsername($username, $password, $remember = false)
    {
        $temp_member = null;
        try {
            $temp_member = $this->memberDAO->getMemberByUsername($username, true);
        } catch (\PDOException $ex) {
            $this->log->error("PDO exception occurred. $ex->getMessage()");
        }
        if (!$temp_member) {
            $this->log->debug('Member not found.', array('username' => $username));
            $this->member = null;
            $this->session = null;
            return false;
        } else {
            $hashed_pwd = $temp_member->getHashedPassword();
            if (password_verify($password, $hashed_pwd)) {
                $this->member = $temp_member;
                if ($this->generateSessionForMember($remember) == SessionService::SESSION_ACTIVE) {
                    return true;
                } else {
                    $this->log->error('Error generating new session for user.', ['username' => $username]);
                    return false;
                }
            } else {
                $this->log->debug('Invalid password for member', array('username' => $username));
                return false;
            }
        }
    }

    /**
     * Authenticates a user by email.
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return bool true on success, false otherwise
     */
    public function authenticateUserByEmail($email, $password, $remember = false)
    {
        $temp_member = null;
        try {
            $temp_member = $this->memberDAO->getMemberByEmail($email, true);
        } catch (\PDOException $ex) {
            $this->log->error("PDO exception occurred. $ex->getMessage()");
        }
        if (!$temp_member) {
            $this->log->debug('Member not found.', array('email' => $email));
            $this->member = null;
            $this->session = null;
            return false;
        } else {
            $hashed_pwd = $temp_member->getHashedPassword();
            if (password_verify($password, $hashed_pwd)) {
                $this->member = $temp_member;
                $this->generateSessionForMember($remember);
                return true;
            } else {
                $this->log->debug('Invalid password for member', array('email' => $email));
                $this->member = null;
                return false;
            }
        }
    }

    /**
     * Returns true if a user is authenticated.
     * @return bool
     */
    public function isAuthenticated()
    {
        return ($this->member != null && $this->session != null);
    }

    /**
     * Tells whether the currently authenticated member is an admin.
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->member != null) {
            return $this->member->isAdmin();
        }
        return false;
    }

    /**
     * Gets the currently authenticated member
     * @return \Powon\Entity\Member|null
     */
    public function getAuthenticatedMember()
    {
        return $this->member;
    }

    /**
     * Saves the current session in the database
     * @return bool
     */
    public function saveSession()
    {
        if (!$this->session)
            return false;
        try {
            if ($this->sessionDAO->updateSession($this->session))
                return true;
            else
                return false;
        } catch (\PDOException $ex) {
            $this->log->error("A PDO Exception occurred. $ex->getMessage()");
            return false;
        }
    }

    /**
     * Gets the current Session entity.
     * @return \Powon\Entity\Session|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Destroys the current session (if any)
     * from the database.
     * @return bool True on success, false otherwise.
     */
    public function destroySession()
    {
        if ($this->session) {
            $token = $this->session->getToken();
            $this->log->debug('Deleting session with $token.', array('token'=>$token));
            $this->member = null;
            $this->session = null;
            $this->sessionState = SessionService::SESSION_ENDED;
            try {
                return $this->sessionDAO->deleteSession($token);
            } catch (\PDOException $ex) {
                $this->log->warning("A PDO exception occurred while trying to delete session. $ex->getMessage()",
                    [
                        'user' => $this->session->getMemberId(),
                        'token' => $token
                    ]
                );
            }
        }
        return false;
    }

    /**
     * Destroys all sessions for the current user.
     * @return bool True on success, false otherwise.
     */
    public function destroyAllSessions()
    {
        if ($this->member) {
            $id = $this->member->getMemberId();
            $this->log->debug('Deleting all sessions for user id.', array('member_id' => $id));
            $this->session = null;
            $this->member = null;
            $this->sessionState = SessionService::SESSION_ENDED;
            try {
                return $this->sessionDAO->deleteAllSessionsForMember($id);
            } catch (\PDOException $ex) {
                $this->log->warning("A PDO exception occurred while trying to delete all sessions. $ex->getMessage()",
                    ['member_id' => $id]
                );
            }
        }
        return false;
    }

    /**
     * Sets the expiration time of the unused tokens.
     * Tokens not used after this time will become invalid or garbage collected.
     * @param int $seconds
     * @return void
     */
    public function setExpiration($seconds)
    {
        $this->expiration = $seconds;
    }

    /**
     * @return int The time in seconds after the last access when the token expires.
     */
    public function getTokenValidityPeriod()
    {
        return $this->expiration;
    }

    /**
     * Eliminates expired sessions.
     */
    public function garbageCollect()
    {
        $latestValidTime = time() - $this->expiration;
        try {
            $this->sessionDAO->deleteSessionsWithLastAccessSmallerThan($latestValidTime);
        } catch (\PDOException $ex) {
            $this->log->warning("A PDO exception occurred while trying to delete expired sessions. $ex->getMessage()",
                ['latestValidTime' => $latestValidTime]
            );
        }
    }

    /**
     * Sets a key to use when generating tokens.
     * @param string $newKey
     * @return void
     */
    public function setKey($newKey)
    {
        $this->key = $newKey;
    }

    /**
     * To properly expire the browser cookie, the middleware must know
     * if the user has logged out in this request.
     * @return int SESSION_ACTIVE, SESSION_EXPIRED, SESSION_DOES_NOT_EXIST, SESSION_LOGOUT
     */
    public function getSessionState()
    {
        return $this->sessionState;
    }
}
