<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\GroupDAO as GroupDAO;
use \Powon\Entity\Group as Group;

class GroupDaoImpl implements GroupDAO {

    /**
     * GroupDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
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
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Group($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $entity
     * @return Group
     */
    public function createNewGroup($group)
    {
        $sql = 'INSERT INTO powon_group(powon_group_id, group_title, description,
                date_created, group_picture, group_owner) VALUES (:group_id, :grp_title, 
                :description, :date_created, :picture, :owner)
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group->getGroupID(), \PDO::PARAM_STR);
        $stmt->bindValue(':grp_title', $group->getGroupTitle(), \PDO::PARAM_STR);
        $stmt->bindValue(':description', $group->getDescription(), \PDO::PARAM_STR);
        $stmt->bindValue(':date_created', $group->getDateCreated(), \PDO::PARAM_STR);
        $stmt->bindValue(':picture', $group->getGroupPicture(), \PDO::PARAM_STR);
        $stmt->bindValue(':owner', $group->getGroupOwner(), \PDO::PARAM_STR);
        return $stmt->execute();
    }
    /**
     * @param $owner_id
     * @return Group[]|null
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
        $stmt->execute();
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
        WHERE g.group_title LIKE :input';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':input', '%'.$input.'%', \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Group($row);
        },$results);
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
                WHERE :id = i.member_id AND g.powon_group_id = i.powon_group_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Group($row);
        },$results);
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
                  g.powon_group_id, 
                       g2.group_title,
                       g2.description,
                       g2.date_created,
                       g2.group_picture,
                       g2.group_owner
                    FROM powon_group g2, is_group_member i2
                    WHERE :id = i2.member_id AND g2.powon_group_id = i2.powon_group_id
                )';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Group($row);
        },$results);
    }
}
