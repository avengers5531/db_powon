<?php

namespace Powon\Entity;


class Region
{
    private $region_id;
    private $country;
    private $province;
    private $city;

    /**
     *
     */
    public function __construct($data)
    {
        $this->region_id = $data['region_id'];
        $this->country = $data['country'];
        $this->province = $data['province'];
        $this->city = $data['city'];
    }

    /**
     * @return string
     */
    public function getRegionId()
    {
        return $this->region_id;
    }

    public function setRegionId($region_id)
    {
        return $this->region_id = $region_id;
    }

    public function getRegionCountry()
    {
        return $this->country;
    }
    public function getRegionProvince()
    {
        return $this->province;
    }
    public function getRegionCity()
    {
        return $this->city;
    }
    
    
    public function toObj() {
        $obj = array( 'region_id' => $this->region_id,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city
        );
        return $obj;
    }

}
