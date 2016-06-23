<?php

namespace Powon\Entity;

class Member
{
    protected $member_id;
    protected $username;
    protected $first_name;
    protected $last_name;
    protected $user_email;
    protected $date_of_birth;
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
            $this->member_id = $data['member_id'];
        }
        $this->username = $data['username'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->user_email = $data['user_email'];
        $this->date_of_birth = $data['date_of_birth'];
    }

    public function getMemberId() {
        return $this->member_id;
    }
    public function getUsername() {
        return $this->username;
    }
    public function getFirstName() {
        return $this->first_name;
    }
    public function getLastName() {
        return $this->last_name;
    }
    public function getUserEmail() {
        return $this->user_email;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

}