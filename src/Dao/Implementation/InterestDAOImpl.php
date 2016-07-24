<?php

namespace Powon\Dao\Implementation;


use Powon\Dao\InterestDAO;
use Powon\Entity\Interest;

class InterestDAOImpl implements InterestDAO
{
    
    private $db;

    /**
     * InterestDAOImpl constructor.
     * @param $pdo \PDO
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Gets the interest entity associated with the name
     * @param $name string The interest name
     * @return Interest|null
     */
    public function getInterestByName($name)
    {
        $sql = 'SELECT interest_name FROM interests WHERE interest_name = :name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return new Interest($results);
        }
        return null;
    }

    /**
     * Gets the list of interests for the given member id.
     * @param $id int the member id
     * @return [Interest]
     */
    public function getInterestsForMember($id)
    {
        $sql = 'SELECT interest_name FROM has_interests WHERE member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
           return array_map(function($it) {
               return new Interest($it);
           }, $results); 
        } else {
            return [];
        }
    }

    /**
     * @param $interest Interest
     * @return bool true on success, otherwise false
     */
    public function createInterest($interest)
    {
        $sql = 'INSERT INTO interests(interest_name) VALUES (:name)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $interest->getName());
        return $stmt->execute();
    }

    /**
     * @param $interest Interest
     * @param $member int The member id
     * @return bool true on success, failure otherwise
     */
    public function addInterestForMember($interest, $member)
    {
        $sql = 'SELECT * FROM has_interests WHERE interest_name = :name AND member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $interest->getName());
        $stmt->bindValue(':id', $member);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
           return true;
        } else {
            $sql = 'INSERT INTO has_interests(interest_name, member_id)
                VALUES (:name, :id)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $interest->getName());
            $stmt->bindValue(':id', $member);
            return $stmt->execute();
        }
    }
    
    public function RemoveInterestByNam($name)
    {
        $sql = 'DELETE FROM interests WHERE interest_name = :name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        return $stmt->execute();
    }

    public function RemoveInterestByNamForMamber($name, $member)
    {
        $sql = 'DELETE FROM has_interests WHERE interest_name = :name AND member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':id', $member);
        return $stmt->execute();
    }

    /**
     * @return [Interest]
     */
    public function getAllInterests()
    {
        $sql = 'SELECT * FROM interests';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
           return array_map(function($it) {
               return new Interest($it);
           }, $results); 
        } else {
            return [];
        }
    }
}
