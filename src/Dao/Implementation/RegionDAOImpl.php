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
        $sql = 'SELECT * FROM region WHERE region_id = :id';
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
        $sql = 'SELECT * FROM region WHERE country = :country';
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
        $sql = 'SELECT * FROM region WHERE country = :city';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':city', $city, \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return new Region($results);
        }
        return null;
    }
    
    

    public function createRegion($region)
    {
        $sql = 'INSERT INTO region(country,province,city) VALUES (:country,:province,:city)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':country', $region->getRegionCountry());
         $stmt->bindValue(':province', $region->getRegionProvince());
          $stmt->bindValue(':city', $region->getRegionCity());
        return $stmt->execute();
    }

    public function RemoveRegion($region_id)
    {
        $sql = 'DELETE FROM region WHERE region_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $region_id);
        return $stmt->execute();
    }

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return [WorkAs]
     */
    public function getRegionForMember($id)
    {
        $sql = 'SELECT r.region_id,                         
                r.country,
                       r.province,
                       r.city
            FROM region r NATURAL JOIN member m
            WHERE m.member_id = :id AND r.region_id = m.lives_in';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Region($row) : null);
        } else {
            return null;
        }
    }

    public function getRegionByCPC($region){
        $sql = 'SELECT * FROM region WHERE country = :country AND province = :province AND city = :city';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':country', $region->getRegionCountry(), \PDO::PARAM_STR);
        $stmt->bindParam(':province', $region->getRegionProvince(), \PDO::PARAM_STR);
        $stmt->bindParam(':city', $region->getRegionCity(), \PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch();
        if ($results) {
            return $results['region_id'];
        } else {
            return null;
        }
    }

    public function updateRegionForMember($region, $member_id){
        $region_id = $this->getRegionByCPC($region);
        if ($r == null) {
            $this->createRegion($region);
            $region_id = $this->getRegionByCPC($region);
        }
        $sql = 'UPDATE member SET lives_in = :lives_in WHERE member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lives_in', $region_id, \PDO::PARAM_INT);
        $stmt->bindValue(':id', $member_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }
}
