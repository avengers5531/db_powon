<?php

namespace Powon\Dao\Implementation;


use Powon\Dao\RegionDAO;
use Powon\Entity\Region;

class RegionDAOImpl implements RegionDAO
{
    
    private $db;

    /**
     * RegionDAOImpl constructor.
     * @param $pdo \PDO
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Gets the Region entity associated with the name
     * @param $name string The Region name
     * @return Region|null
     */
    public function getRegionId($region_id)
    {
        $sql = 'SELECT * FROM Region WHERE region_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $region_id, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return new Region($results);
        }
        return null;
    }
    
     public function getRegionByCountry($country)
    {
        $sql = 'SELECT * FROM Region WHERE country = :country';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':country', $country, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return new Region($results);
        }
        return null;
    }
    
     public function getRegionByCity($city)
    {
        $sql = 'SELECT * FROM Region WHERE country = :city';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':city', $city, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return new Region($results);
        }
        return null;
    }
    
    

    public function createRegion($resion)
    {
        $sql = 'INSERT INTO Region(country,province,city) VALUES (:country,province,city)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':country', $resion->getRegionCountry());
         $stmt->bindValue(':province', $resion->getRegionProvince());
          $stmt->bindValue(':city', $resion->getRegionCity());
        return $stmt->execute();
    }

    public function RemoveRegion($region_id)
    {
        $sql = 'DELETE FROM Region WHERE region_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $region_id);
        return $stmt->execute();
    }
}
