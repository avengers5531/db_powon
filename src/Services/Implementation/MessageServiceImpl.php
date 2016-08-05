<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Message;
use Powon\Entity\Member;
use Powon\Services\MessageService;
use Powon\Dao\MessageDAO;
use Powon\Dao\MemberDAO;
use Psr\Log\LoggerInterface;


class MessageServiceImpl implements MessageService{
    /**
    * @param member Member
    * @return array of messages
    */
    /**
     * @var MessageDAO
     */
    private $messageDAO;

    /**
     * @var MemberDAO
     */
    private $memberDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger,
                                MessageDAO $dao,
                                MemberDAO $memberDAO)
    {
        $this->messageDAO = $dao;
        $this->log = $logger;
        $this->memberDAO = $memberDAO;
    }

    public function getMessagesForMember(Member $member){
        try{
            $messages = $this->messageDAO->getMessagesForMember($member);
            if ($messages){
                foreach ($messages as &$message) {
                    $this->populateMessageAuthor($message);
                }
            }
            return $messages;
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
    }

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesSentByMember(Member $member){
        try{
            return $this->messageDAO->getMessagesSentByMember($member);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
    }

    /**
    * @param msg: a Message object
    */
    public function sendMessage(Message $msg, array $usernames){
        if ($usernames){
            foreach ($usernames as $name) {
                $member = $this->memberDAO->getMemberByUsername($name);
                $msg->addRecipient($member);
            }
            try{
                return $this->messageDAO->sendMessage($msg);
            } catch (\PDOException $ex) {
                $this->log->error("A pdo exception occurred: " . $ex->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
    * Mark message as read
    * @param to Member
    * @param msg Message
    */
    public function readMessage(Member $to, Message $msg){
        try{
            return $this->messageDAO->readMessage($member);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return false;
        }
    }

    /**
    * Mark message as deleted in member's inbox
    * @param to Member
    * @param msg Message
    */
    public function deleteMessage(Member $to, Message $msg){
        try{
            return $this->messageDAO->deleteMessage($member);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return false;
        }
    }

    public function populateMessageAuthor(Message $msg){
        if ($msg) {
            try {
                $author = $this->memberDAO->getMemberById($msg->getAuthorId());
                $msg->setAuthor($author);
            } catch (\PDOException $ex) {
                $this->log->error('A PDO exception prevented getting the author for message. '
                    . $ex->getMessage());
            }
        }
    }
}
