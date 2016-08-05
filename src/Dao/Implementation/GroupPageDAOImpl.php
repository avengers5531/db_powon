<?php


namespace Powon\Dao\Implementation;
use \Powon\Dao\GroupPageDAO as GroupPageDAO;
use \Powon\Entity\GroupPage as GroupPage;
use \Powon\Entity\Member as Member;
class GroupPageDAOImpl implements GroupPageDAO
{

    private $db;

    /**
     * GroupPageDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @param $page_id
     * @return GroupPage|null
     */
    public function getGroupPageById($page_id)
    {
        $sql = 'SELECT g.page_id,
                       p.page_title,
                       p.date_created,
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group
                FROM group_page g, page p
                WHERE g.page_id = :page_id AND p.page_id = g.page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new GroupPage($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $page_title
     * @return GroupPage|null
     */
    public function getGroupPageByTitle($page_title)
    {
        $sql = 'SELECT g.page_id,
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group
                FROM group_page g, page p
                WHERE p.page_title = :page_title AND p.page_id = g.page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_title', $page_title, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new GroupPage($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $group_id
     * @return [GroupPage]
     */
    public function getPagesOfGroup($group_id)
    {
        $sql = 'SELECT g.page_id,
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group,
                       p.page_title,
                       p.date_created
                FROM group_page g INNER JOIN page p ON p.page_id = g.page_id
                WHERE g.page_group = :group_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
            return array_map(function($data) {
                return new GroupPage($data);
            }, $rows);
        } else {
            return [];
        }
    }

    /**
     * @param $group_page GroupPage
     * @return int The id of the newly created group page
     */
    public function createGroupPage($group_page)
    {
        $title = $group_page->getPageTitle();
        $group_page_id = 0;
        $sql1 = 'INSERT INTO page(page_title) VALUES(:title)';
        $sql2 = 'INSERT INTO group_page(page_id, page_description, access_type, page_owner, page_group)
                 VALUES (:id, :description, :access_type, :owner, :group_id)';
        $stmt = $this->db->prepare($sql1);
        $stmt->bindValue(':title', $title, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $group_page_id = $this->db->lastInsertId();
        }
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->bindValue(':id', $group_page_id, \PDO::PARAM_STR);
        $stmt2->bindValue(':description', $group_page->getPageDescription(), \PDO::PARAM_STR);
        $stmt2->bindValue(':access_type', $group_page->getPageAccessType(), \PDO::PARAM_STR);
        $stmt2->bindValue(':owner', $group_page->getPageOwner(), \PDO::PARAM_STR);
        $stmt2->bindValue(':group_id', $group_page->getPageGroupId(), \PDO::PARAM_STR);
        if($stmt2->execute()){
            return $group_page_id;
        }
        
        return 0;
    }

    /**
     * @param $page_id
     * @return bool
     */
    public function deleteGroupPage($page_id)
    {
        $sql = 'DELETE FROM group_page WHERE page_id = :page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $page_id
     * @param $input
     * @return bool
     */
    public function updateGroupPageTitle($page_id, $input)
    {
        $sql = 'UPDATE page p 
                SET page_title = :input
                WHERE page_id = :page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':input', $input, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $page_id
     * @param $input
     * @return bool
     */
    public function updateGroupPageDescription($page_id, $input)
    {
        $sql = 'UPDATE group_page g
                SET page_description = :input
                WHERE page_id = :page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':input', $input, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return null|\Powon\Entity\GroupPage[]
     */
    public function getGroupPagesForMember($member_id, $group_id)
    {
        $sql = 'SELECT g.page_id,
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group,
                       p.page_title,
                       p.date_created
                FROM group_page g INNER JOIN page p ON p.page_id = g.page_id
                WHERE g.page_group = :group_id AND g.access_type = \'E\' OR (
                g.access_type = \'P\' AND EXISTS (SELECT * 
                    FROM member_can_access_page a
                    WHERE a.page_id = g.page_id AND a.member_id = :member_id AND g.page_group = a.powon_group_id)                   
                )';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new GroupPage($row);
                },$results);
            } else{
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * @param $page_id
     * @return Member[]|null
     */
    public function getMembersWithPageAccess($page_id)
    {
        $sql = 'SELECT m.member_id,
                       m.first_name,
                       m.last_name,
                       m.username,
                       m.user_email,
                       m.status,
                       m.date_of_birth
                FROM member m, member_can_access_page a, group_page g
                WHERE g.page_id = :page_id AND g.page_id = a.page_id AND  a.member_id = m.member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new Member($row);
                },$results);
            } else{
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * @param $page_id
     * @param $owner_id
     * @return bool
     */
    public function deleteGroupPageMembers($page_id, $owner_id)
    {
        $sql = 'DELETE FROM member_can_access_page WHERE page_id = :page_id AND member_id <> :owner_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':owner_id', $owner_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $page_id
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function addMemberToGroupPage($page_id, $member_id, $group_id)
    {
        $sql = 'INSERT INTO member_can_access_page(page_id, powon_group_id, member_id) 
                VALUES (:page_id, :group_id, :member_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $page_id
     * @param $access_type
     * @return mixed
     */
    public function updateAccessType($page_id, $access_type)
    {
        $sql = 'UPDATE group_page
                SET access_type = :access_type
                WHERE page_id = :page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':access_type', $access_type, \PDO::PARAM_STR);
        return $stmt->execute();
    }
}
