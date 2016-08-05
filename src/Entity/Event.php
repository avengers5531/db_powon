<?php

namespace Powon\Entity;


use MongoDB\BSON\Timestamp;

class Event
{

    private $event_id;
    private $event_title;
    private $event_description;
    private $event_date;
    private $event_time;
    private $event_location;

    /**
     * Event constructor. Accepts an array of data for attributes
     * of this class and creates the class.
     * @param array $data
     */
    public function __construct(array $data){
        if(isset($data['event_id'])) {
            $this->group_id = (int)$data['powon_group_id'];
        }
        $this->event_title = $data['title'];
        $this->event_description = $data['description'];
        $this->event_date = $data['event_date'];
        $this->event_time = $data['event_time'];
        $this->event_location = $data['location'];
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * @return string
     */
    public function getEventTitle()
    {
        return $this->event_title;
    }

    /**
     * @return string
     */
    public function getEventDescription()
    {
        return $this->event_description;
    }

    /**
     * @return Timestamp
     */
    public function getEventDate()
    {
        return $this->event_date;
    }

    /**
     * @return Timestamp
     */
    public function getEventTime()
    {
        return $this->event_time;
    }

    /**
     * @return string
     */
    public function getEventLocation()
    {
        return $this->event_location;
    }

    /**
     * @param $title
     */
    public function setEventTitle($title){
        $this->event_title = $title;
    }

    /**
     * @param $description
     */
    public function setEventDescription($description)
    {
        $this->event_description = $description;
    }

    /**
     * @param $date
     */
    public function setEventDate($date)
    {
        $this->event_date = $date;
    }

    public function setEventTime($time)
    {
        $this->event_time = $time;
    }

    /**
     * @param $location
     */
    public function setEventLocation($location)
    {
        $this->event_location = $location;
    }

    public function toObject() {
        $obj = array();
        if (isset($this->event_id)) {
            $obj['event_id'] = $this->event_id;
        }
        $obj['title'] = $this->event_title;
        $obj['description'] = $this->event_description;
        $obj['event_date'] = $this->event_date;
        $obj['event_time'] = $this->event_time;
        $obj['location'] = $this->event_location;
        return $obj;
    }

    public function toJson() {
        return json_encode($this->toObject());
    }

}