<?php

namespace Powon\Dao\Implementation;


use Powon\Dao\ProfessionDAO;
use Powon\Entity\Profession;

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
}
