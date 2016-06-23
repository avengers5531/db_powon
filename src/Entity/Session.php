<?php

namespace Powon\Entity;

class Session {
    private $token;

    private $member_id;
    
    private $last_access;
    
    private $session_data;
    
    public function __construct($data)
    {
        if (isset($data['token'])) {
            $this->token = $data['token'];
        } else {
            $this->token = null;
        }
        $this->member_id = $data['member_id'];
        $this->last_access = $data['last_access'];
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
}