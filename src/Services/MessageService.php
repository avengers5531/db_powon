<?php

namespace Powon\Services;

use Powon\Entity\Message;
use Powon\Entity\Member;

interface MessageService{

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
    * @param member Member
    * @param msg Message
    * @return bool is member a recipient of the message in question
    */
    public function isRecipient(Member $member, Message $msg);

    /**
    * @param member Member: the author of the message
    * @param params array: array of form params
    */
    public function sendMessage(Member $member, $params);

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

    /**
    * generate a member object for the author based on the author's id
    * @param msg Message
    */
    public function populateMessageAuthor(Message $msg);

    /**
    * generate member objects for the members_to array of a Message
    * @param msg Message
    */
    public function populateRecipients(Message $msg);
}
