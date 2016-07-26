<?php

namespace Powon\Entity;


class Is_group_member
{
    private $group_id;
    private $member_id;
    private $request_date;
    private $approval_date;

    public function __construct(array $data){
        $this->group_id = $data[`group_id`];
        $this->member_id = $data[`member_id`];
        $this->request_date = $data[`request_date`];
        $this->approval_date = $data['approval_date'];
    }

    /**
     * @return int
     */
    public function getGroupId(){
        return $this->group_id;
    }

    /**
     * @return int
     */
    public function getMemberId(){
        return $this->member_id;
    }

    /**
     * @return date
     */
    public function getRequestDate(){
        return $this->request_date;
    }

    /**
     * @return date
     */
    public function getApprovalDate(){
        return $this->approval_date;
    }

    public function toObject() {
        $obj = array();
        if (isset($this->group_id)) {
            $obj['powon_group_id'] = $this->group_id;
        }
        if (isset($this->group_id)) {
            $obj['member_id'] = $this->member_id;
        }
        return $obj;
    }

    public function toJson() {
        return json_encode($this->toObject());
    }

}