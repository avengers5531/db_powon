<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\MessageDAO as MessageDAO;
use \Powon\Entity\Message as Message;
use \Powon\Entity\Member as Member;

class MessageDAOImpl implements MessageDAO {
    private $db;

    /**
     * MessageDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesForMember(Member $member){
        $sql = "SELECT message_id,
                from_member,
                subject,
                body,
                message_seen,
                message_deleted
                FROM messages_to NATURAL JOIN messages
                WHERE member_id = :mid
                AND message_deleted = 'N'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mid', $member->getMemberId(), \PDO::PARAM_INT);
        if ($stmt->execute()){
            $row = $stmt->fetch();
            return ($row ? new Message($row) : null);
        }
        return null;
    }

    /**
    * @param member Member
    * @return array of messages
    */
    public function getMessagesSentByMember(Member $member){
        $sql = 'SELECT message_id,
                message_timestamp,
                subject,
                body,
                from_member
                FROM messages
                WHERE from_member = :mid';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mid', $member->getMemberId(), \PDO::PARAM_INT);
        if ($stmt->execute()){
            $row = $stmt->fetch();
            return ($row ? new Message($row) : null);
        }
        return null;
    }

    /**
    * @param msg: A Message Object
    */
    public function sendMessage(Message $msg){
        $sql = 'INSERT INTO messages (subject, body, from_member)
                VALUES (:subject, :body, :from_member)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':subject', $msg->getSubject(), \PDO::PARAM_STR);
        $stmt->bindValue(':body', $msg->getBody(), \PDO::PARAM_STR);
        $stmt->bindValue(':from_member', $msg->getAuthorId(), \PDO::PARAM_STR);
        if ($stmt->execute()){
            $last_id = $this->db->lastInsertId();
            foreach ($msg->getRecipients() as $member) {
                $sql = 'INSERT INTO messages_to (message_id, member_id)
                        VALUES (:message_id, :member_id)';
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':message_id', $last_id, \PDO::PARAM_INT);
                $stmt->bindValue(':member_id', $member->getMemberId(), \PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    /**
    * Mark message as read
    * @param to Member
    * @param msg Message
    */
    public function readMessage(Member $to, Message $msg){
        $sql = "UPDATE messages_to SET message_seen = 'Y'
                WHERE message_id = :message_id
                AND member_id = :member_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $msg->getMessageId(), \PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $to->getMemberId(), \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
    * Mark message as deleted for receiving Member
    * @param to Member
    * @param msg Message
    */
    public function deleteMessage(Member $to, Message $msg){
        $sql = "UPDATE messages_to SET message_deleted = 'Y'
                WHERE message_id = :message_id
                AND member_id = :member_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $msg->getMessageId(), \PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $to->getMemberId(), \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
