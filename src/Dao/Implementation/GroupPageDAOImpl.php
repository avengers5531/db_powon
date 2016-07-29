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
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group
                FROM group_page g
                WHERE g.page_id = :page_id';
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
     * @return GroupPage|null
     */
    public function getPagesOfGroup($group_id)
    {
        $sql = 'SELECT g.page_id,
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group
                FROM group_page g
                WHERE g.page_group = :group_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new GroupPage($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $group_page GroupPage
     * @return int The id of the newly created group page
     */
    public function createGroupPage($group_page)
    {
        $sql = 'INSERT INTO group_page(page_id, page_description, access_type, page_owner, page_group)
                VALUES (:page_id, :description, :access_type, :owner, :group_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $group_page->getPageId(), \PDO::PARAM_STR);
        $stmt->bindValue(':description', $group_page->getPageDescription(), \PDO::PARAM_STR);
        $stmt->bindValue(':access_type', $group_page->getPageAccessType(), \PDO::PARAM_STR);
        $stmt->bindValue(':owner', $group_page->getPageOwner(), \PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $group_page->getPageGroupId(), \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
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
                WHERE p.page_id = :page_id';
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
                WHERE g.page_id = :page_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':input', $input, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $page_id
     * @param $member_id
     * @return GroupPage[]|null
     */
    public function getGroupPagesForMember($page_id, $member_id)
    {
        $sql = 'SELECT g.page_id,
                       g.page_description,
                       g.access_type,
                       g.page_owner,
                       g.page_group
                FROM group_page g, member m, member_can_access_page a
                WHERE g.page_id = :page_id AND g.page_id = a.page_id AND a.member_id = :member_id AND a.member_id = m.member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', '%'.$page_id.'%', \PDO::PARAM_STR);
        $stmt->bindValue(':member_id', '%'.$member_id.'%', \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            if(!empty($results)){
                return array_map(function ($row) {
                    return new GroupPage($row);
                },$results);
            } else{
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $page_id
     * @param $member_id
     * @return Member[]|null
     */
    public function getMembersWithPageAccess($page_id, $member_id)
    {
        $sql = 'SELECT m.member_id,
                       m.first_name,
                       m.last_name
                FROM member m, member_can_access_page a, group_page g
                WHERE g.page_id = :page_id AND g.page_id = a.page_id AND a.member_id = :member_id AND a.member_id = m.member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', '%'.$page_id.'%', \PDO::PARAM_STR);
        $stmt->bindValue(':member_id', '%'.$member_id.'%', \PDO::PARAM_STR);
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
     * @param $page_id
     * @param $member_id
     * @return bool
     */
    public function deleteGroupPageMembers($page_id, $member_id)
    {
        $sql = 'DELETE FROM member_can_access_page WHERE page_id = :page_id AND :member_id NOT IN (SELECT g.page_owner 
                                                                            FROM group_page g
                                                                            WHERE g.page_id = :page_id AND g.page_owner = :member_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':page_id', $page_id, \PDO::PARAM_STR);
        $stmt->bindValue(':member_id', $page_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }
}