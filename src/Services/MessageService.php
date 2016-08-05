<?php

namespace Powon\Services;

use Powon\Entity\Message;
use Powon\Entity\Member;

interface MessageService{

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
        * @param msg: a Message object
        * @param array string: an array of recipient usernames
        */
        public function sendMessage(Message $msg, array $usernames);

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

        public function populateMessageAuthor(Message $msg);
}
