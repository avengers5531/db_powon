<?php

namespace Powon\Entity;

class MemberPage
{
    private $page_id;
    private $date_created;
    private $title;
    private $member_id;
    private $page_access;

    //TODO should title attribute be moved to group page in the model?

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['page_id'])) {
            $this->page_id = (int)$data['page_id'];
        }
        if(isset($data['date_created'])){
          $this->date_created = $data['date_created'];
        }
        $this->member_id = $data['member_id'];
        $this->title = $data['title'];
        $this->page_access = $data['page_access'];
        //TODO Make default page access?
    }

    /**
     * @return int
     */
    public function getPageId() {
        return $this->page_id;
    }

    /**
     * @return int
     */
    public function getMemberId() {
        return $this->member_id;
    }

    /**
     * @return string
     */
    public function dateCreated() {
        return $this->date_created;
    }

    /**
     * @return string
     */
    public function title() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function page_access() {
        return $this->last_name;
    }

    /**
     * @return array the member entity in php array format (note it does not include the hashed password).
     */
    public function toObject() {
        $obj = array();
        if (isset($this->page_id)) {
            $obj['page_id'] = $this->page_id;
        }
        if (isset($this->date_created)) {
            $obj['date_created'] = $this->date_created;
        }
        $obj['member_id'] = $this->member_id;
        $obj['title'] = $this->title;
        $obj['page_access'] = $this->page_access;

        return $obj;
    }

    /**
     * @return string the member entity in json format
     */
    public function toJson() {
       return json_encode($this->toObject());
    }
}
