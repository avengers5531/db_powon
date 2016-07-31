<?php

namespace Powon\Entity;


class Interest
{
    private $name;

    /**
     *
     */
    public function __construct($data)
    {
        $this->name = $data['interest_name'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function toObj() {
        $obj = ['interest_name' => $this->name];
        return $obj;
    }

}
