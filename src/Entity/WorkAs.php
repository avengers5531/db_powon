<?php

namespace Powon\Entity;


class WorkAs
{
    private $member_id;
    private $profession_name;
    private $date_started;
    private $date_ended;

    /**
     *
     */
    public function __construct($data)
    {
        $this->member_id = $data['member_id'];
        $this->profession_name = $data['profession_name'];
        $this->date_started = $data['date_started'];
        $this->date_ended = $data['date_ended'];
    }

    /**
     * @return string
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->profession_name;
    }

    /**
     * @return string
     */
    public function getDateStarted()
    {
        return $this->date_started;
    }

    /**
     * @return string
     */
    public function getDateEnded()
    {
        return $this->date_ended;
    }
    
    public function toObj() {
        $obj = ['profession_name' => $this->profession_name];
        $obj = ['date_started' => $this->date_started];
        $obj = ['date_ended' => $this->date_ended];
        return $obj;
    }

}
