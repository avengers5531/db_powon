<?php

namespace Powon\Entity;

class Session {
    /**
     * The authentication token
     * @var string
     */
    private $token;

    /**
     * The member's database id.
     * @var int
     */
    private $member_id;

    /**
     * Timestamp of the last access.
     * @var int
     */
    private $last_access;

    /**
     * @var array arbitrary session data (key/value) pairs
     */
    private $session_data;
    
    public function __construct($data)
    {
        if (isset($data['token'])) {
            $this->token = $data['token'];
        } else {
            $this->token = null;
        }
        $this->member_id = $data['member_id'];
        $this->last_access = (int)$data['last_access'];
        if (isset($data['session_data'])) {
            $this->session_data = $data['session_data'];
        } else {
            $this->session_data = array();
        }
    }
    
    public function getLastAccess() {
        return $this->last_access;
    }
    
    public function getToken() {
        return $this->token;
    }
    
    public function getSessionData() {
        return $this->session_data;
    }
    
    public function getMemberId() {
        return $this->member_id;
    }

    /**
     * @param int $time timestamp
     */
    public function setLastAccess($time) {
        $this->last_access = $time;
    }

    /**
     * @return array session entity in php array format.
     */
    public function toObject() {
        $obj = array(
            'token' => $this->token,
            'member_id' => $this->member_id,
            'last_access' => $this->last_access,
            'session_data' => $this->session_data
        );
        
        return $obj;
    }

    /**
     * @return string session entity in json format.
     */
    public function toJson() {
        return json_encode($this->toObject());
    }
}