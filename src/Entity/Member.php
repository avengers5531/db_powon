<?php

namespace Powon\Entity;

class Member
{
    private $member_id;
    private $username;
    private $first_name;
    private $last_name;
    private $user_email;
    private $date_of_birth;
    private $hashed_pwd;
    private $is_admin;
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
        $this->user_email = $data['user_email'];
        $this->date_of_birth = $data['date_of_birth'];
        if (isset($data['password'])) {
            $this->hashed_pwd = $data['password'];
        }
        if (isset($data['is_admin']) && strcmp($data['is_admin'], 'Y') === 0) {
            $this->is_admin = true;
        } else {
            $this->is_admin = false;
        }
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
     * @return string
     */
    public function getUserEmail() {
        return $this->user_email;
    }

    /**
     * @return string The user's date of birth
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * @return string|null the hashed_password
     */
    public function getHashedPassword() {
        return $this->hashed_pwd;
    }

    /**
     * @return bool
     */
    public function isAdmin() {
        return $this->is_admin;
    }

    /**
     * @param username string
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
    * @param fname string
     */
    public function setFirstName($fname) {
        $this->first_name = $fname;
    }

    /**
    * @param lname string
     */
    public function setLastName($lname) {
        $this->last_name = $lname;
    }

    /**
    * @param email string
     */
    public function setUserEmail($email) {
        $this->user_email = $email;
    }

    /**
    * @param dob string
     */
    public function setDateOfBirth($dob)
    {
        $this->date_of_birth = $dob;
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

        return $obj;
    }

    /**
     * @return string the member entity in json format
     */
    public function toJson() {
       return json_encode($this->toObject());
    }

}
