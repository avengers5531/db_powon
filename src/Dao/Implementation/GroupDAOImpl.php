<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\GroupDAO as GroupDAO;
use \Powon\Entity\Group as Group;

class GroupDaoImpl implements GroupDAO {

    private $db;

    /**
     * GroupDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Get group details via group id
     * @param $id
     * @return Group|null
     */
    public function getGroupById($id)
    {
        $sql = 'SELECT g.powon_group_id, 
                       g.group_title,
                       g.description,
                       g.date_created,
                       g.group_picture,
                       g.group_owner
        FROM powon_group g
        WHERE powon_group_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Group($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $group Group
     * @return int The id of the newly created group
     */
    public function createNewGroup($group)
    {
        $sql = 'INSERT INTO powon_group(group_title, description,
                group_picture, group_owner) VALUES (:grp_title, 
                :description, :picture, :owner)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':grp_title', $group->getGroupTitle(), \PDO::PARAM_STR);
        $stmt->bindValue(':description', $group->getDescription(), \PDO::PARAM_STR);
        $stmt->bindValue(':picture', $group->getGroupPicture(), \PDO::PARAM_STR);
        $stmt->bindValue(':owner', $group->getGroupOwner(), \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return 0;
    }

    /**
     * @param $owner_id
     * @return Group|null
     */
    public function getGroupByOwnerId($owner_id)
    {
        $sql = 'SELECT g.powon_group_id, 
                       g.group_title,
                       g.description,
                       g.date_created,
                       g.group_picture,
                       g.group_owner
                FROM powon_group g
                WHERE group_owner = :owner_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':owner_id', $owner_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Group($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $input
     * @return Group[]|null
     */
    public function searchGroupByTitle($input)
    {
        $sql = 'SELECT g.powon_group_id, 
                       g.group_title,
                       g.description,
                       g.date_created,
                       g.group_picture,
                       g.group_owner
        FROM powon_group g
        WHERE g.group_title LIKE :input OR g.description LIKE :input';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':input', '%'.$input.'%', \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new Group($row);
                },$results);
            } else{
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @return Group[]|null
     */
    public function getGroupsMemberBelongsTo($id)
    {
        $sql = 'SELECT g.powon_group_id, 
                       g.group_title,
                       g.description,
                       g.date_created,
                       g.group_picture,
                       g.group_owner
                FROM powon_group g, is_group_member i 
                WHERE :id = i.member_id AND g.powon_group_id = i.powon_group_id
                AND i.approval_date IS NOT NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);

        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new Group($row);
                },$results);
            } else{
                return null;
            }
        } else {
            return null;
        }
        /*
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Group($row);
        },$results);
        */
    }

    /**
     * 
     * @param $id
     * @return Group[]|null
     */
    public function getGroupsMemberNotBelongsTo($id)
    {
        $sql = 'SELECT g.powon_group_id, 
                       g.group_title,
                       g.description,
                       g.date_created,
                       g.group_picture,
                       g.group_owner
                FROM powon_group g 
                NOT IN(
                  SELECT  g2.powon_group_id, 
                          g2.group_title,
                          g2.description,
                          g2.date_created,
                          g2.group_picture,
                          g2.group_owner
                  FROM powon_group g2, is_group_member i2
                  WHERE :id = i2.member_id AND g2.powon_group_id = i2.powon_group_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new Group($row);
                },$results);
            } else{
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteGroup($id)
    {
        $sql = 'DELETE FROM powon_group WHERE powon_group_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupTitle($id, $input)
    {
        $sql = 'UPDATE powon_group g
                SET group_title = :input
                WHERE powon_group_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->bindValue(':input', $input, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupDescription($id, $input)
    {
        $sql = 'UPDATE powon_group g
                SET description = :input
                WHERE powon_group_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->bindValue(':input', $input, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $member_id int the member's id.
     * @param $group_id int the group id.
     * @return bool true on success, false otherwise
     */
    public function isMemberInGroup($member_id, $group_id)
    {
        $sql = 'SELECT member_id from is_group_member WHERE member_id = :member_id AND powon_group_id = :group_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':member_id',$member_id, \PDO::PARAM_INT);
        $stmt->bindParam(':group_id',$group_id, \PDO::PARAM_INT);
        if($stmt->execute()) {
            $res = $stmt->fetch();
            if ($res) {
                return true;
            }
        }
        return false;
    }
}
