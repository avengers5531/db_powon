<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\RelationshipDAO as RelationshipDAO;
use Powon\Entity\FriendRequest;

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
        $stmt->bindParam(':midA', $member_from);
        $stmt->bindParam(':midB', $member_to);
        $stmt->bindParam(':rel', $rel_type);
        return $stmt->execute();
    }

    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    */
    public function confirmRelationship($member_from, $member_to){
        $dt = new DateTime();
        $sql = 'UPDATE related_members SET approval_date = :now
                WHERE member_from = :mfrom AND member_to = :mto';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':now', $dt->format('Y-m-d H:i:s'));
        $stmt->bindParam(':mfrom', $member_from);
        $stmt->bindParam(':mto', $member_to);
        return $stmt->execute();
    }

    /**
    * Get pending relationship requests for a specific member
    * @param mid int: member id of the requested party
    * @return array of members with the requested relationship type
    */
    public function getPendingRelRequests($mid){
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
                WHERE m.member_id = r.member_to
                AND member_id = :id
                AND approval_date IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @return string relationship if exists, else null
    */
    public function checkRelationship($mid1, $mid2){
        $sql = 'SELECT rel_type FROM related_members
                WHERE member_from = :midA AND member_to = :midB
                AND approval_date IS NOT NULL
                UNION
                SELECT rel_type FROM related_members
                WHERE member_from = :midB AND member_to = :midA
                AND approval_date IS NOT NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':midA', $mid1);
        $stmt->bindParam(':midB', $mid2);
        if $stmt->execute(){
            $relationship = $stmt->fetch();
            return $relationship
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
    public function updateRelationship($mid1, $mid2, $rel_type){}

}
