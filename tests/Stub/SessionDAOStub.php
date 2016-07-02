<?php
namespace Powon\Test\Stub;


use Powon\Dao\SessionDAO;
use Powon\Entity\Session;

/**
 * Class SessionDAOStub
 * @package Powon\Test\Stub
 * Stub for Session Data Access Object
 */
class SessionDAOStub implements SessionDAO
{

    /**
     * @var array An array of Mock sessions data.
     */
    public $sessions;

    public function __construct()
    {
        $this->sessions = [];
    }

    /**
     * @param string $token
     * @return Session|null A Session entity object if found. null otherwise.
     */
    public function getSession($token)
    {
        foreach ($this->sessions as &$item) {
            if (strcmp($item['token'], $token) === 0) {
                return new Session($item);
            }
        }
        return null;
    }

    /**
     * @param Session $session
     * @return Session|false The Session entity or false on failure.
     */
    public function updateSession(Session $session)
    {
        foreach ($this->sessions as &$item) {
            if (strcmp($item['token'], $session->getToken()) === 0) {
                $item = $session->toObject();
                break;
            }
        }
        return $session;
    }

    /**
     * @param Session $session
     * @return Session|false The Session entity or false on failure.
     */
    public function createSession(Session $session)
    {
        $this->sessions[] = $session->toObject();
        return $session;
    }

    /**
     * @param string $token
     * @return bool True on success, false on failure.
     */
    public function deleteSession($token)
    {
        $initial_size = count($this->sessions);
        $this->sessions = array_filter($this->sessions,
            function ($it) use ($token) {
                return $it['token'] !== $token;
            }
        );
        $final_size = count($this->sessions);
        return $initial_size !== $final_size;
    }

    /**
     * @param int $member_id
     * @return bool True on success, False on failure
     */
    public function deleteAllSessionsForMember($member_id)
    {
        $initial_size = count($this->sessions);
        $this->sessions = array_filter($this->sessions,
            function ($it) use ($member_id) {
                return $it['member_id'] !== $member_id;
            }
        );
        $final_size = count($this->sessions);
        return $initial_size !== $final_size;
    }

    /**
     * Clean up method to delete old tokens.
     * @param int $access_time
     * @return bool
     */
    public function deleteSessionsWithLastAccessSmallerThan($access_time)
    {
        $initial_size = count($this->sessions);
        $this->sessions = array_filter($this->sessions,
            function ($it) use ($access_time) {
                return $it['last_access'] <= $access_time;
            }
        );
        $final_size = count($this->sessions);
        return $initial_size !== $final_size;
    }
}