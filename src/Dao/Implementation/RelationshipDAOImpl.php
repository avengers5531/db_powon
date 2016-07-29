<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\RelationshipDAO as RelationshipDAO;
use \Powon\Entity\FriendRequest;
use \Powon\Entity\Member;

class RelationshipDAOImpl implements RelationshipDAO{
    private $db;

    /**
     * RelationshipDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }
    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function requestRelationship($member_from, $member_to, $rel_type){
        $sql = 'INSERT INTO related_members(member_from, member_to,
                relation_type)
                VALUES
                (:midA, :midB, :rel)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':midA', $member_from->getMemberId());
        $stmt->bindValue(':midB', $member_to->getMemberId());
        $stmt->bindValue(':rel', $rel_type);
        return $stmt->execute();
    }

    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    */
    public function confirmRelationship($member_from, $member_to){
        $sql = 'UPDATE related_members SET approval_date = :now
                WHERE member_from = :mfrom AND member_to = :mto';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':now', date("Y-m-d H:i:s", time()));
        $stmt->bindParam(':mfrom', $member_from->getMemberId());
        $stmt->bindParam(':mto', $member_to->getMemberId());
        return $stmt->execute();
    }

    /**
    * Get pending relationship requests for a specific member
    * @param mid int: member id of the requested party
    * @return array of members with the requested relationship type
    */
    public function getPendingRelRequests(Member $member){
        $mid = $member->getMemberId();
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.is_admin,
                m.profile_picture,
                r.relation_type,
                r.request_date
                FROM member AS m, related_members AS r
                WHERE m.member_id = r.member_from
                AND r.member_to = :id
                AND approval_date IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $mid, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new FriendRequest($row);
        },$results);
    }

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @return string relationship if exists, else null
    */
    public function checkRelationship(Member $member1, Member $member2){
        $mid1 =  $member1->getMemberId();
        $mid2 =  $member2->getMemberId();
        $sql = 'SELECT rel_type, approval_date FROM related_members
                WHERE member_from = :midA AND member_to = :midB
                AND request_date IS NOT NULL
                UNION
                SELECT rel_type FROM related_members
                WHERE member_from = :midB AND member_to = :midA
                AND request_date IS NOT NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':midA', $mid1, \PDO::PARAM_INT);
        $stmt->bindParam(':midB', $mid2, \PDO::PARAM_INT);
        if ($stmt->execute()){
            $relationship = $stmt->fetch();
            return $relationship;
        }
        else{
            return null;
        }
    }

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function updateRelationship($member1, $member2, $rel_type){}

}
