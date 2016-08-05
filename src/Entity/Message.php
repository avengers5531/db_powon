<?php

namespace Powon\Entity;

class Message{
    private $message_id;
    private $message_timestamp
    private $from_member_id;
    private $members_to;
    private $subject;
    private $body;
    private $is_seen;
    private $is_deleted;

    public function __construct(array $data){
        if(isset($data['message_id'])) {
            $this->message_id = (int)$data['message_id'];
        }
        if(isset($data['message_timestamp'])) {
            $this->message_timestamp = (int)$data['message_timestamp'];
        }
        $this->from_member_id = $data['from_member'];
        $this->subject = $data['subject'];
        $this->body = $datya['body'];
        if(isset($data['is_seen'])) {
            $this->is_seen = (int)$data['is_seen'];
        }
        if(isset($data['is_deleted'])) {
            $this->is_deleted = (int)$data['is_deleted'];
        }
    }

    public function getMessageId(){
        return $this->message_id;
    }

    public function getSenderId(){
        return $this->from_member_id;
    }

    public function getSubject(){
        return $this->subject;
    }

    public function getBody(){
        return $this->body;
    }

    public function setSubject($text){
        $this->subject = $text;
    }

    public function setBody($text){
        $this->body = $text;
    }
