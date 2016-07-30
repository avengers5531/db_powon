<?php

namespace Powon\Entity;


class Profession
{
    private $name;

    /**
     *
     */
    public function __construct($data)
    {
        $this->name = $data['profession_name'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function toObj() {
        $obj = ['profession_name' => $this->name];
        return $obj;
    }

}
