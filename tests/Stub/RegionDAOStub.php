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

    public function getRegionId($region) {
        foreach ($this->region as &$region) {
            if ($region['region_id'] === $region) {
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

}
