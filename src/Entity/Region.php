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
        $obj = ['region_name' => $this->name];
        return $obj;
    }

}
