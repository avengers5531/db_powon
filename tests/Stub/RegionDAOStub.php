<?php

/**
 * Created by IntelliJ IDEA.
 * User: link
 * Date: 2016-07-10
 * Time: 1:28 PM
 */

namespace Powon\Test\Stub;

use Powon\Dao\RegionDAO;
use Powon\Entity\Region;

class RegionDAOStub implements RegionDAO {

    public $region;
    public $lives_in;
    public $region_id = 0;

    public function getRegionId($id) {
        foreach ($this->region as &$region) {
            if ($region['region_id'] == $id) {
                return new Region($region);
            }
        }
        return null;
    }

    public function getRegionByCountry($region) {
        foreach ($this->region as &$region) {
            if ($region['country'] === $region) {
                return new Region($region);
            }
        }
        return null;
    }

    public function getRegionByCity($region) {
        foreach ($this->region as &$region) {
            if ($region['city'] === $region) {
                return new Region($region);
            }
        }
        return null;
    }

    public function createRegion($region) {
        $this->region[] = $region->toObj();
        return true;
    }

    public function RemoveRegion($region) {
        return true;
    }

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return [WorkAs]
     */
    public function getRegionForMember($id)
    {
        $results = [];
        if(count($this->lives_in)>0){
            foreach ($this->lives_in as &$row) {
                if ($row['member_id'] === $id) {
                    $results = $this->getRegionId($row['lives_in']);
                }
            }
        }
        return $results;
    }

    public function getRegionByCPC($region){
        return true;
    }

    public function updateRegionForMember($region, $member_id){
        $old_region = $this->getRegionForMember($member_id);
        if (count($old_region) > 0) {
           foreach ($this->lives_in as &$row) {
                if ($row['member_id'] === $workAs->getMemberId()) {
                    $row['lives_in'] = $old_region->getRegionId();
                }
            }
        } else {
            $region->setRegionId($this->region_id++);
            $this->createRegion($region);
            foreach ($this->region as &$row) {
                if ($row['country'] === $region->getRegionCountry() && $row['province'] === $region->getRegionProvince() && $row['city'] === $region->getRegionCity()) {

                    $this->lives_in[] = ['lives_in' => $row['region_id'],
                        'member_id' => $member_id
                    ];
                }
            }
        }
    }
}
