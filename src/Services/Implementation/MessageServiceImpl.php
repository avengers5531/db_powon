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

    /**
    * @param message_id int
    * @return a Message object
    */
    public function getMessageById($message_id){
        try{
            $msg = $this->messageDAO->getMessageById($message_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return null;
        }
        $this->populateMessageAuthor($msg);
        $this->populateRecipients($msg);
        return $msg;
    }

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesForMember(Member $member){
        try{
            $messages = $this->messageDAO->getMessagesForMember($member);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
        if ($messages){
            foreach ($messages as &$message) {
                $this->populateMessageAuthor($message);
            }
        }
        return $messages;
    }

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesSentByMember(Member $member){
        try{
            $messages = $this->messageDAO->getMessagesSentByMember($member);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
        if ($messages){
            foreach ($messages as &$message) {
                $this->populateRecipients($message);
            }
        }
        return $messages;

    }

    /**
    * @param member Member
    * @param msg Message
    * @return bool is member a recipient of the message in question
    */
    public function isRecipient(Member $member, Message $msg){
        try{
            return $this->messageDAO->isRecipient($member, $msg);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return false;
        }
    }

    /**
    * @param member Member: the author of the message
    * @param params array of form params
    */
    public function sendMessage(Member $member, $params){
        $data = array('from_member' =>  $member->getMemberId(),
                      'subject' => $params["subject"],
                      'body' => $params["body"]
        );
        $msg = new Message($data);
        $usernames = explode(', ', $params["to"]);
        if ($usernames){
            foreach ($usernames as $name) {
                $member = $this->memberDAO->getMemberByUsername($name);
                if($member){
                    $msg->addRecipient($member);
                }
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
            return $this->messageDAO->readMessage($to, $msg);
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

    /**
    * generate a member object for the author based on the author's id
    * @param msg Message
    */
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

    /**
    * generate member objects for the members_to array of a Message
    * @param msg Message
    */
    public function populateRecipients(Message $msg){
        if ($msg) {
            try {
                $recipients = $this->messageDAO->getRecipients($msg);
            } catch (\PDOException $ex) {
                $this->log->error('A PDO exception prevented getting the author for message. '
                    . $ex->getMessage());
            }
        }
        if ($recipients){
            foreach ($recipients as $mid) {
                try{
                    $recipient = $this->memberDAO->getMemberById($mid);
                    $msg->addRecipient($recipient);
                } catch (\PDOException $ex) {
                    $this->log->error('A PDO exception prevented getting the author for message. '
                        . $ex->getMessage());
                }
            }
        }
    }
}
