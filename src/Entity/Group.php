<?php

namespace Powon\Entity;


class Group
{

    private $group_id;
    private $group_title;
    private $description;
    private $date_created;
    private $group_picture;
    private $group_owner;

    /**
     * Group constructor. Accepts an array of data for attributes
     * of this class and creates the class.
     * @param array $data
     */
    public function __construct(array $data){
        if(isset($data['group_id'])) {
            $this->member_id = (int)$data['group_id'];
        }
        $this->group_title = $data[`group_title`];
        $this->description = $data[`description`];
        $this->date_created = $data[`date_created`];
        $this->group_picture = $data['group_picture'];
        $this->group_owner = $data['group_owner'];
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * @return string
     */
    public function getGroupTitle()
    {
        return $this->group_title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return timestamp
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @return string
     */
    public function getGroupPicture()
    {
        return $this->group_picture;
    }

    /**
     * @return int
     */
    public function getGroupOwner()
    {
        return $this->group_owner;
    }
/*
    public function toObject() {
        $obj = array();
        if (isset($this->group_id)) {
            $obj['group_id'] = $this->group_id;
        }
        $obj['group_title'] = $this->group_title;
        $obj['description'] = $this->description;
        $obj['date_of_birth'] = $this->date_created;
        $obj['group_picture'] = $this->group_picture;
        $obj['group_owner'] = $this->group_owner;
        return $obj;
    }

    public function toJson() {
        return json_encode($this->toObject());
    }
*/
}