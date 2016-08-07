<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\GiftWantedDAO as GiftWantedDAO;
use \Powon\Entity\GiftWanted as GiftWanted;
use Powon\Utils\DateTimeHelper;


class GiftWantedDAOImpl implements GiftWantedDAO
{

    private $db;

    /**
     * GiftWantedDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @param $member_id
     * @return array of GiftWanted entities
     */
    public function getWishListById($member_id)
    {
        $sql = 'SELECT *
                FROM wish_list
                WHERE member_id = :member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':member_id', $member_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
            if (count($rows) > 0) {
                return array_map(function($data) {
                    return new GiftWanted($data);
                }, $rows);
            }
        }
        return [];
    }

    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function giveGift($member_id, $gift_name)
    {
        $sql = 'UPDATE wish_list
                SET date_received = :currentdate
                WHERE member_id = :member_id
                AND gift_name = :gift_name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':currentdate', DateTimeHelper::getCurrentDate(), \PDO::PARAM_STR);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_INT);
        $stmt->bindValue(':gift_name', $gift_name, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function requestGift($member_id, $gift_name)
    {
        $sql = 'INSERT INTO wish_list (member_id, gift_name) 
            VALUES (:member_id, :gift_name)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_INT);
        $stmt->bindValue(':gift_name', $gift_name, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        } else return false;
    }

    public function verifyGiftExists($gift_name)
    {
        $sql = 'SELECT gift_name
        FROM gift_inventory
        WHERE gift_name = :gift_name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':gift_name', $gift_name, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $res = $stmt->fetch();
            if ($res)
                return true;
        }
        return false;
    }

    /**
     * List of gifts in the inventory
     * @return [string]
     */
    public function getGiftList()
    {
        $stmt = $this->db->prepare('SELECT gift_name FROM gift_inventory');
        if ($stmt->execute()) {
            $items = $stmt->fetchAll();
            if (count($items) > 0) {
                return array_map(function($item) {
                    return $item['gift_name'];
                }, $items);
            }
        }
        return [];
    }

    /**
     * Removes the gifts from the wish list.
     * @param $member_id int
     * @return bool
     */
    public function removeGiftsForMember($member_id) {
        $sql = 'DELETE FROM wish_list WHERE member_id = :member_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id);
        return $stmt->execute();
    }
}

