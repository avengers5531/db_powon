<?php

namespace Powon\Entity;

class GroupPage
{
    const ACCESS_EVERYONE = 'E';
    const ACCESS_PRIVATE = 'P';

    /**
     * @var $page_id int
     */
    private $page_id;

    /**
     * @var $page_title string
     */
    private $page_title;

    /**
     * @var $date_created string
     */
    private $date_created;

    /**
     * @var $access_type string
     */
    private $access_type;

    /**
     * @var $page_description string
     */
    private $page_description;

    /**
     * @var $page_owner int The member id of the page owner
     */
    private $page_owner;

    /**
     * @var $page_group int The group id.
     */
    private $page_group;

    public function __construct($data)
    {
        if (isset($data['page_id'])) {
            $this->page_id = $data['page_id'];
        }
        $this->page_title = $data['page_title'];
        $this->page_description = $data['page_description'];
        $this->page_group = $data['page_group'];
        $this->page_owner = $data['page_owner'];
        $this->access_type = $data['access_type'];
        if (isset($data['date_created'])) {
            $this->date_created = $data['date_created'];
        }
    }

    /**
     * @return int
     */
    public function getPageId() {
        return $this->page_id;
    }
    /**
     * @return string
     */
    public function getPageTitle() {
        return $this->page_title;
    }
    /**
     * @return int
     */
    public function getPageOwner() {
        return $this->page_owner;
    }
    /**
     * @return int
     */
    public function getPageGroupId() {
        return $this->page_group;
    }
    /**
     * @return string
     */
    public function getPageDescription() {
        return $this->page_description;
    }
    /**
     * @return string
     */
    public function getDateCreated() {
        return $this->date_created;
    }
    /**
     * @return string
     */
    public function getPageAccessType() {
        return $this->access_type;
    }

    /**
     * @param $description string
     */
    public function setGroupPageDescription($description){
        $this->$this->page_description = $description;
    }

    /**
     * @param $access_type string
     */
    public function setAccessType($access_type){
        $this->access_type = $access_type;
    }

    /**
     * @param $page_owner int
     */
    public function setPageOwner($page_owner){
        $this->page_owner = $page_owner;
    }

    public function toObject() {
        $obj = array();
        if (isset($this->group_id)) {
            $obj['page_id'] = $this->page_id;
        }
        $obj['page_title'] = $this->page_title;
        $obj['description'] = $this->page_description;
        $obj['date_created'] = $this->date_created;
        $obj['group_owner'] = $this->page_owner;
        $obj['access_type'] = $this->access_type;
        $obj['page_group'] = $this->page_group;
        return $obj;
    }

    public function toJson() {
        return json_encode($this->toObject());
    }

}
