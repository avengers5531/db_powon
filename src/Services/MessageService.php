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

        public function populateMessageAuthor(Message $msg);
}
