<?php

namespace Powon\Entity;

class FriendRequest
{
    private $member_from;
    private $member_to;
    private $username;
    private $first_name;
    private $last_name;
    private $profile_picture;
    private $relation_type;
    private $request_date;
    private $approval_date;
    //TODO the other attributes

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        $this->member_from = (int)$data['member_from'];
        if (isset($data['member_to'])){
            $this->member_to = (int)$data['member_to'];
        }
        if (isset($data['username'])){
            $this->username = $data['username'];
        }
        if (isset($data['first_name'])){
            $this->first_name = $data['first_name'];
        }
        if (isset($data['last_name'])){
            $this->last_name = $data['last_name'];
        }
        if (isset($data['profile_picture'])){
            $this->profile_picture = $data['profile_picture'];
        }
        if (isset($data['relation_type'])){
            $this->relation_type = $data['relation_type'];
        }
        if (isset($data['request_date'])){
            $this->request_date = $data['request_date'];
        }
        if (isset($data['approval_date'])){
            $this->approval_date = $data['approval_date'];
        }
    }

    /**
     * @return int
     */
    public function getMemberFrom() {
        return $this->member_from;
    }

    /**
     * @return int
     */
    public function getMemberTo() {
        return $this->member_to;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->last_name;
    }

    /**
     * @return picpath string : a path to a picture.
     */
    public function getProfilePic(){
        return $this->profile_picture;
    }

    public function getRelationType(){
        switch ($this->relation_type){
            case 'F':
                return "Friend";
            case 'I':
                return "Immediate Family";
            case 'E':
                return "Extended Family";
            case 'C':
                return "Colleague";
            default:
                break;
        }
    }

    /**
    * Returns the relation type as an integer, for bitwise comparison for
    * access of different elements on member profile pages.
    */
    public function getRelationTypeAsInt(){
        if (approval_date){
            switch ($this->relation_type){
                case 'F':
                    return 17;
                case 'I':
                    return 9;
                case 'E':
                    return 5;
                case 'C':
                    return 3;
                default:
                    break;
            }
        }
        return 1;
    }

    public function getRequestDate(){
        return $this->request_date;
    }

    public function getApprovalDate(){
        return $this->approval_date;
    }


    /**
     * @return array the member entity in php array format (note it does not include the hashed password).
     */
    public function toObject() {
        $obj = array();
        if (isset($this->member_from)) {
            $obj['member_from'] = $this->member_from;
        }
        $obj['username'] = $this->username;
        $obj['first_name'] = $this->first_name;
        $obj['last_name'] = $this->last_name;
        $obj['user_email'] = $this->user_email;
        $obj['date_of_birth'] = $this->date_of_birth;
        $obj['is_admin'] = $this->is_admin;
        $obj['profile_picture'] = $this->profile_picture;

        return $obj;
    }

    /**
     * @return string the member entity in json format
     */
    public function toJson() {
       return json_encode($this->toObject());
    }

}
