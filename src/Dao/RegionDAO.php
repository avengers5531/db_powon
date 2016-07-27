<?php

namespace Powon\Dao;


use Powon\Entity\Region;

interface RegionDAO
{

    /**
     * Gets the Region entity associated with the name 
     * @param $region_id string The Region name
     * @return Region|null
     */
    public function getRegionId($region_id);

    public function getRegionByCountry($country);
    
    public function getRegionByCity($city);
    /**
     * @param $region Region
     * @return bool true on success, otherwise false
     */
    public function createRegion($region);
    
    public function RemoveRegion($region_id);

    public function getRegionByCPC($region);

    public function getRegionForMember($member_id);

    public function updateRegionForMember($region, $member_id);

}
