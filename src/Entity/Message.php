<?php

namespace Powon\Entity;

use Powon\Entity\Member;

class Message{
    private $message_id;
    private $message_timestamp;
    private $from_member_id;
    private $members_to;
    private $subject;
    private $body;
    private $is_seen;
    private $is_deleted;
    // Member object for the message author
    private $author;

    public function __construct(array $data){
        if(isset($data['message_id'])) {
            $this->message_id = (int)$data['message_id'];
        }
        if(isset($data['message_timestamp'])) {
            $this->message_timestamp = (int)$data['message_timestamp'];
        }
        $this->from_member_id = $data['from_member'];
        $this->subject = $data['subject'];
        $this->body = $data['body'];
        if(isset($data['members_to'])){
            $this->members_to = [];
        }
        if(isset($data['is_seen'])) {
            $this->is_seen = (int)$data['is_seen'];
        }
        if(isset($data['is_deleted'])) {
            $this->is_deleted = (int)$data['is_deleted'];
        }
    }

    /**
    * @return int the id of the message
    */
    public function getMessageId(){
        return $this->message_id;
    }

    /**
    * @return int the id of the author of the message
    */
    public function getAuthorId(){
        return $this->from_member_id;
    }

    /**
    * @return Member the author of the message
    */
    public function getAuthor(){
        return $this->author;
    }

    /**
    * @return string the subject of the message
    */
    public function getSubject(){
        return $this->subject;
    }

    /**
    * @return string the body of the message
    */
    public function getBody(){
        return $this->body;
    }

    /**
    * @return array of members receiving the email
    */
    public function getRecipients(){
        return $this->members_to;
    }

    /**
    * @return the timestamp that the message was sent
    */
    public function getTimestamp(){
        return $this->message_timestamp;
    }

    /**
    * @param text string the subject line of the message
    */
    public function setSubject($text){
        $this->subject = $text;
    }

    /**
    * @param text string the body text of the message
    */
    public function setBody($text){
        $this->body = $text;
    }

    /**
    * @param author a Member object representing the author of the message
    */
    public function setAuthor(Member $author){
        $this->author = $author;
    }

    /**
    * @param members an array of Member objects
    */
    public function setRecipients(array $members){
        $this->members_to = $members;
    }

    /**
    * Add a recipient to the array of recipients
    * @param member a Member object
    */
    public function addRecipient(Member $member){
        $this->members_to[] = $member;
    }
}
