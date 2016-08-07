<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\IsGroupMemberDAO as IsGroupMemberDAO;
use \Powon\Entity\Member as Member;
use Powon\Entity\Group;
class IsGroupMemberDAOImpl implements IsGroupMemberDAO
{
    private $db;

    /**
     * MemberDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberBelongsToGroup($member_id, $group_id)
    {
        $sql = 'SELECT i.member_id,
                       i.powon_group_id
                FROM is_group_member i
                WHERE i.member_id = :member_id AND i.powon_group_id = :group_id
                AND i.approval_date IS NOT NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $res = $stmt->fetch();
            if ($res)
                return true;
        }
        return false;
    }

    /**
     * Checks whether a member is waiting for an approval
     * @param $member_id int the member id
     * @param $group_id int the group id
     * @return bool True if member is waiting for approval, false otherwise
     */
    public function memberWaitingForApprovalToGroup($member_id, $group_id)
    {
        $sql = 'SELECT i.member_id,
                       i.powon_group_id
                FROM is_group_member i
                WHERE i.member_id = :member_id AND i.powon_group_id = :group_id
                AND i.approval_date IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $res = $stmt->fetch();
            if ($res)
                return true;
        }
        return false;
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberRequestsToJoinGroup($member_id, $group_id)
    {
        $sql = 'INSERT INTO is_group_member(powon_group_id, member_id)
                VALUES(:powon_group_id, :member_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_STR);
        $stmt->bindValue(':powon_group_id', $group_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $group_id
     * @return \Powon\Dao\Member[]
     */
    public function membersWaitingApproval($group_id)
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.status
                FROM member m, is_group_member i 
                WHERE i.powon_group_id = :group_id AND i.approval_date IS NULL
                AND m.member_id = i.member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new Member($row);
                },$results);
            } else{
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function acceptMemberIntoGroup($member_id, $group_id)
    {
        $sql = 'UPDATE is_group_member i
                SET approval_date = CURRENT_TIMESTAMP
                WHERE i.member_id = :member_id AND i.powon_group_id = :group_id AND approval_date IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_INT);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param $group_id
     * @return \Powon\Dao\Member[]
     */
    public function membersInGroup($group_id)
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.status,
                m.profile_picture
                FROM member m, is_group_member i 
                WHERE i.powon_group_id = :group_id AND i.approval_date IS NOT NULL
                AND m.member_id = i.member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new Member($row);
                },$results);
            } else{
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function deleteMemberFromGroup($member_id, $group_id)
    {
        $sql = 'DELETE FROM is_group_member
                WHERE member_id = :member_id and powon_group_id = :group_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_INT);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function addMemberToGroup($member_id, $group_id)
    {
        $sql = 'INSERT INTO is_group_member(powon_group_id, member_id, approval_date)
                VALUES(:group_id, :member_id, CURRENT_TIMESTAMP )';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
