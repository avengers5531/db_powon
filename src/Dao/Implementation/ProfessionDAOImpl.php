<?php

namespace Powon\Dao\Implementation;


use Powon\Dao\ProfessionDAO;
use Powon\Entity\Profession;
use Powon\Entity\WorkAs;

class ProfessionDAOImpl implements ProfessionDAO
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
    public function getProfessionByName($profession_name)
    {
        $sql = 'SELECT profession_name FROM Profession WHERE profession_name = :profession_name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':profession_name', $profession_name, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return new Profession($results);
        }
        return null;
    }

    public function createProfession($interest)
    {
        $sql = 'INSERT INTO Profession(profession_name) VALUES (:profession_name)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':profession_name', $interest->getName());
        return $stmt->execute();
    }

    public function RemoveProfession($profession_name)
    {
        $sql = 'DELETE FROM Profession WHERE profession_name = :profession_name';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':profession_name', $profession_name);
        return $stmt->execute();
    }

    /**
     * @return [Profession]
     */
    public function getAllProfessions()
    {
        $sql = 'SELECT * FROM profession';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
           return array_map(function($it) {
               return new Profession($it);
           }, $results); 
        } else {
            return [];
        }
    }

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return [WorkAs]
     */
    public function getProfessionForMember($id)
    {
        $sql = 'SELECT * FROM works_as WHERE member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
           $results = array_map(function($it) {
               return new WorkAs($it);
           }, $results);
           return $results[0];
        } else {
            return [];
        }
    }

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return bool true on success, failure otherwise
     */
    public function updateProfessionForMember($workAs){
        $sql = 'SELECT * FROM works_as WHERE member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $workAs->getMemberId());
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
           $sql = 'UPDATE works_as SET profession_name = :name, date_started = :date_started, date_ended = :date_ended WHERE member_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $workAs->getName(), \PDO::PARAM_INT);
            $stmt->bindValue(':id', $workAs->getMemberId(), \PDO::PARAM_STR);
            $stmt->bindValue(':date_started', $workAs->getDateStarted());
            $stmt->bindValue(':date_ended', $workAs->getDateEnded());
            return $stmt->execute();
        } else {
            $sql = 'INSERT INTO works_as(member_id, profession_name, date_started, date_ended)
                VALUES (:id, :name, :date_started, :date_ended)';
            $stmt = $this->db->prepare($sql);
            echo $sql;
            $stmt->bindValue(':name', $workAs->getName());
            $stmt->bindValue(':id', $workAs->getMemberId());
            $stmt->bindValue(':date_started', $workAs->getDateStarted());
            $stmt->bindValue(':date_ended', $workAs->getDateEnded());
            return $stmt->execute();
        }
    }
}
