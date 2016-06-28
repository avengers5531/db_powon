<?php

namespace Powon\Dao\Implementation;

use Powon\Dao\SessionDAO;
use Powon\Entity\Session;

/**
 * Class SessionDAOImpl
 * A standard implementation of the SessionDAO
 * @package Powon\Dao\Implementation
 */
class SessionDAOImpl implements SessionDAO
{
    /**
     * @var \PDO
     */
    private $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $token
     * @return Session|null A Session entity object if found. null otherwise.
     */
    public function getSession($token)
    {
        $sql = 'SELECT s.token, s.member_id, s.last_access, s.session_data FROM
        member_session s WHERE s.token= :token';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            if (isset($data['session_data'])) {
                $data['session_data'] = json_decode($data['session_data']);
            }
            return new Session($data);
        } else {
            return null;
        }
    }

    /**
     * @param Session $session
     * @return Session|false The Session entity or false on failure.
     */
    public function updateSession(Session $session)
    {
        $sql = 'UPDATE member_session SET last_access = :last_access,
                session_data = :session_data
                WHERE
                token = :token';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':last_access', $session->getLastAccess() ,\PDO::PARAM_INT);
        $stmt->bindValue(':session_data', json_encode($session->getSessionData()), \PDO::PARAM_STR);
        $stmt->bindValue(':token', $session->getToken(), \PDO::PARAM_STR );
        if ($stmt->execute()) {
            return $session;
        } else {
            return false;
        }
    }

    /**
     * @param Session $session
     * @return Session|false The Session entity or false on failure.
     */
    public function createSession(Session $session)
    {
        $sql = 'INSERT INTO member_session(token, member_id, last_access, session_data) VALUES 
                (:token, :member_id, :last_access, :session_data)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token', $session->getToken(), \PDO::PARAM_STR);
        $stmt->bindValue(':member_id', $session->getMemberId(), \PDO::PARAM_INT);
        $stmt->bindValue(':last_access', $session->getLastAccess(), \PDO::PARAM_INT );
        $stmt->bindValue(':session_data', json_encode($session->getSessionData()));
        if ($stmt->execute()) {
            return $session;
        } else {
            return false;
        }
    }

    /**
     * @param string $token
     * @return bool True on success, false on failure.
     */
    public function deleteSession($token)
    {
        $sql = 'DELETE FROM member_session WHERE token = :token';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $member_id
     * @return bool True on success, False on failure
     */
    public function deleteAllSessionsForMember($member_id)
    {
        $sql = 'DELETE FROM member_session WHERE member_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $member_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Clean up method to delete old tokens.
     * @param int $access_time
     * @return bool
     */
    public function deleteSessionsWithLastAccessSmallerThan($access_time)
    {
        $sql = 'DELETE FROM member_session WHERE last_access < :access_time';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam($stmt, $access_time);
        return $stmt->execute();
    }
}