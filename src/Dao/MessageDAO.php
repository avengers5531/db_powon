<?php

namespace Powon\Dao;

use Powon\Entity\Member;
use Powon\Entity\Message;

interface MessageDAO {

    /**
    * @param message_id int
    * @return a Message object
    */
    public function getMessageById($message_id);

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesForMember(Member $member);

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesSentByMember(Member $member);

    /**
    * @param msg Message
    * @return array of member id's who have received a message
    */
    public function getRecipients(Message $msg);

    /**
    * @param member Member
    * @param msg Message
    * @return bool is member a recipient of the message in question
    */
    public function isRecipient(Member $member, Message $msg);

    /**
    * @param msg: a Message object
    */
    public function sendMessage(Message $msg);

    /**
    * Mark message as read
    * @param to Member
    * @param msg Message
    */
    public function readMessage(Member $to, Message $msg);

    /**
    * Mark message as deleted in member's inbox
    * @param to Member
    * @param msg Message
    */
    public function deleteMessage(Member $to, Message $msg);

}
