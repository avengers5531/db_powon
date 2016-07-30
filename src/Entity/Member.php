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
    private $registration_date;
    private $region_access;
    private $region_id;
    private $country;
    private $province;
    private $city;
    private $interests_access;
    private $professions_access;
    private $profession_id;
    private $profession_name;

    /**
     * @var [Interest]
     */
    private $interests;
    private $profile_picture;

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
        if (isset($data['profile_picture'])){
            $this->profile_picture = $data['profile_picture'];
        }
        if (isset($data['interests_access'])){
            $this->interests_access = $data['interests_access'];
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


    public function region_id() {
        return $this->region_id;
    }
    public function country() {
        return $this->country;
    }
    public function province() {
        return $this->province;
    }
    public function city() {
        return $this->city;
    }



    public function getInterestsAccess() {
        return $this->interests_access;
    }
    public function getProfessionsAccess() {
        return $this->professions_access;
    }
    public function getProfessionId() {
        return $this->profession_id;
    }

    public function setProfessionId($profession_id) {
        $this->profession_id = $profession_id;
    }

    public function getProfessionName() {
        return $this->profession_name;
    }

    public function setProfessionName($profession_name) {
        $this->profession_name = $profession_name;
    }

    public function getProfessionDateStarted() {
        return $this->profession_date_started;
    }

    public function setProfessionDateStarted($profession_date_started) {
        $this->profession_date_started = $profession_date_started;
    }

    public function getProfessionDateEnded() {
        return $this->profession_date_ended;
    }

    public function setProfessionDateEnded($profession_date_ended) {
        $this->profession_date_ended = $profession_date_ended;
    }

    /**
     * @return [Profession]
     */
    public function setWorkAs($workAs) {
        if(!empty($workAs)){
            $this->setProfessionName($workAs->getName());
            $this->setProfessionDateStarted($workAs->getDateStarted());
            $this->setProfessionDateEnded($workAs->getDateEnded());
        }
    }

    /**
     * @return [Interest]
     */
    public function getInterestsArray() {
        $interests = array_map(function($interests) {
            return $interests->getName();
        }, $this->interests);
        return $interests;
    }

    /**
     * @return [Interest]
     */
    public function getInterests() {
        return $this->interests;
    }

    /**
     * @param $interests [Interest]
     */
    public function setInterests($interests) {
        $this->interests = $interests;
    }

    /**
     * @param $interests [Interest]
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @param $interests [Interest]
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     * @return picpath string : a path to a picture.
     */
    public function getProfilePic(){
        return $this->profile_picture;
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
     * @param picpath string : a path to a picture.
     */
    public function setProfilePic($picpath){
        $this->profile_picture = $picpath;
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
        $obj['region_id'] = $this->region_id;
        $obj['country'] = $this->country;
        $obj['province'] = $this->province;
        $obj['city'] = $this->city;
        $obj['interests_access'] = $this->interests_access;
        $obj['professions_access'] = $this->professions_access;
        $obj['profession_id'] = $this->profession_id;
        $obj['profession_name'] = $this->profession_name;
        if ($this->interests) {
            $obj['interests'] = array_map(function ($it) {
                return $it->toObj();
            }, $this->interests);
        } else {
            $obj['interests'] = null;
        }


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
