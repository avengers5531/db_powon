<?php

<?php

namespace Powon\Entity;

class FriendRequest
{
    private $member_id;
    private $username;
    private $first_name;
    private $last_name;
    private $profile_picture;
    private $relation_type;
    private $request_date;
    //TODO the other attributes

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['member_id'])) {
            $this->member_id = (int)$data['member_id'];
        }
        $this->username = $data['username'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->profile_picture = $data['profile_picture'];
        $this->relation_type = $data['relation_type'];
        $this->request_date = $data['request_date'];
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
        return $this->relation_type;
    }

    public function getRequestDate(){
        return $this->request_date;
    }


    /**
     * @return array the member entity in php array format (note it does not include the hashed password).
     */
    public function toObject() {
        $obj = array();
        if (isset($this->member_id)) {
            $obj['member_id'] = $this->member_id;
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
