<?php

namespace Powon\Dao;


use Powon\Entity\Session;

interface SessionDAO
{
    
    /**
     * @param string $token
     * @return Session|null A Session entity object if found. null otherwise.
     */
    public function getSession($token);

    /**
     * @param Session $session
     * @return Session|false The Session entity or false on failure.
     */
    public function updateSession(Session $session);

    /**
     * @param Session $session
     * @return Session|false The Session entity or false on failure.
     */
    public function createSession(Session $session);
    
    /**
     * @param string $token
     * @return bool True on success, false on failure.
     */
    public function deleteSession($token);

    /**
     * @param int $member_id
     * @return bool True on success, False on failure
     */
    public function deleteAllSessionsForMember($member_id);

    /**
     * Clean up method to delete old tokens.
     * @param int $access_time
     * @return bool
     */
    public function deleteSessionsWithLastAccessSmallerThan($access_time);
}