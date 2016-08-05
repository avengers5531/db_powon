<?php

namespace Powon\Services\Implementation;

use Powon\Dao\InterestDAO;
use Powon\Entity\Member;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;
use Powon\Services\MemberService;
use Powon\Dao\MemberDAO;
use Powon\Utils\DateTimeHelper;
use Powon\Dao\RegionDAO;
use Powon\Dao\ProfessionDAO;
use Powon\Entity\Interest;
use Powon\Entity\WorkAs;
use Powon\Entity\Region;

class MemberServiceImpl implements MemberService
{
    /**
     * @var MemberDAO
     */
    private $memberDAO;

    /**
     * @var LoggerInterface
     */
    private $log;


    /**
     * @var InterestDAO
     */
    private $interestDAO;

    private $professionDAO;
    private $regionDAO;


    public function __construct(LoggerInterface $logger, MemberDAO $dao, InterestDAO $interestDAO, ProfessionDAO $professionDAO, RegionDAO $resionDAO)
    {
        $this->memberDAO = $dao;
        $this->log = $logger;
        $this->interestDAO = $interestDAO;
        $this->professionDAO = $professionDAO;
        $this->regionDAO = $resionDAO;
    }

    /**
     * @return Member[] All the members
     */
    public function getAllMembers() {
        try {
            return $this->memberDAO->getAllMembers();
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * Return the Member entity with the given username
     * @param $username [string] the member's username
     */
     public function getMemberByUsername($username){
       try{
         return $this->memberDAO->getMemberByUsername($username);
       } catch (\PDOException $ex) {
         $this->log->error("A pdo exception occured: ". $ex->getMessage());
         return [];
       }
     }

    /**
     * @param $username string
     * @param $user_email string
     * @param $password string
     * @param $date_of_birth string
     * @param $first_name string
     * @param $last_name string
     * @return mixed array('success': bool, 'message':string)
     */
    public function registerNewMember($username,
                                      $user_email,
                                      $password,
                                      $date_of_birth,
                                      $first_name,
                                      $last_name)
    {
        if ($this->memberDAO->getMemberByUsername($username)) {
            $this->log->debug("Username $username exists");
            return array('success'=> false, 'message' =>'Username exists.');
        }
        if ($this->memberDAO->getMemberByEmail($user_email)) {
            $this->log->debug("Email $user_email already exists in the system");
            return array('success'=> false, 'message' =>'Email exists.');
        }
        if (!DateTimeHelper::validateDateFormat($date_of_birth)) {
            $this->log->debug("Invalid format for date: $date_of_birth");
            return array('success'=> false, 'message' =>'Date format must be YYYY-MM-DD.');
        }
        $data = array(
            'username' => $username,
            'user_email' => $user_email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'date_of_birth' => $date_of_birth,
            'status' => 'A' // TODO use const variabales
        );
        $newMember = new Member($data);
        $pwd_hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            if ($this->memberDAO->createNewMember($newMember, $pwd_hash)) {
                $this->log->info('Registered new member',
                    ['username' => $username, 'email' => $user_email]);
                return array('success' => true,
                    'message' => "New member $username was registered.");
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when registering a new user: ". $ex->getMessage());
        }
        return array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    /**
     * Updates the provided member entity with the correct interests
     * @param $member Member
     * @return bool
     */
    public function populateInterestsForMember($member)
    {
        $interests = [];
        try {
            $interests = $this->interestDAO->getInterestsForMember($member->getMemberId());
        } catch (\PDOException $ex) {
            $this->log->error("PDO Exception " . $ex->getMessage());
            return false;
        }
        $member->setInterests($interests);
        return $member;
    }

    /**
     * Updates the provided member entity with the correct profession
     * @param $member Member
     * @return bool
     */
    public function populateProfessionForMember($member)
    {
        $workas = [];
        try {
            $workas = $this->professionDAO->getProfessionForMember($member->getMemberId());
        } catch (\PDOException $ex) {
            $this->log->error("PDO Exception " . $ex->getMessage());
            return false;
        }
        $member->setWorkAs($workas);
        return $member;
    }

    /**
     * Updates the provided member entity with the correct profession
     * @param $member Member
     * @return bool
     */
    public function populateRegionForMember($member)
    {
        $region = [];
        try {
            $region = $this->regionDAO->getRegionForMember($member->getMemberId());
        } catch (\PDOException $ex) {
            $this->log->error("PDO Exception " . $ex->getMessage());
            return false;
        }
        $member->setRegion($region);
        return $member;
    }

    /**
     * Checks whether a member with the given email address, first name and date of birth exists.
     * @param $email
     * @param $first_name
     * @param $date_of_birth
     * @return array ['success' => bool, 'message' => string]
     */
    public function doesMemberExist($email,
                                    $first_name,
                                    $date_of_birth)
    {
        $user = null;
        if (!DateTimeHelper::validateDateFormat($date_of_birth)) {
            $this->log->info("Invalid date format $date_of_birth");
            return [
                'success' => false,
                'message' => "Invalid date format for $date_of_birth. Valid format is YYYY-MM-DD."
            ];
        }
        try {
            $user = $this->memberDAO->getMemberByEmail($email);
        } catch (\PDOException $ex) {
            $this->log->error('A pdo exception occurred when checking if a member with'.
            " email $email exists: ". $ex->getMessage());
        }
        if ($user && $user->getFirstName() === $first_name &&
            $user->getDateOfBirth() === $date_of_birth) {
            return [
                'success' => true,
                'message' => 'Found an existing member.'
            ];
        }
        return [
            'success' => false,
            'message' => 'Did not find a member with the given parameters.'
        ];
    }

    /**
     * Use this method to register a new member to the Powon system.
     * Unlike the registerNewMember method, this one complies
     * with the specifications and checks for an existing member first.
     * @param $params [string] the parameters from the request
     * @return mixed array('success': bool, 'message':string)
     */
    public function registerPowonMember($params)
    {
        $msg = '';
        if (!Validation::validateParametersExist(
            [
                MemberService::FIELD_MEMBER_EMAIL,
                MemberService::FIELD_FIRST_NAME,
                MemberService::FIELD_MEMBER_DATE_OF_BIRTH,
                MemberService::FIELD_EMAIL,
                MemberService::FIELD_USERNAME,
                MemberService::FIELD_PASSWORD,
                MemberService::FIELD_PASSWORD2,
                MemberService::FIELD_DATE_OF_BIRTH,
                MemberService::FIELD_FIRST_NAME,
                MemberService::FIELD_LAST_NAME
            ], $params)
        ) {
            $msg = 'Invalid parameters entered';
            $this->log->debug("Registration failed: $msg", $params);
        } elseif ($params[MemberService::FIELD_PASSWORD] !== $params[MemberService::FIELD_PASSWORD2]) {
                $msg = 'Passwords don\'t match!';
        } else {
            $res = $this->doesMemberExist($params[MemberService::FIELD_MEMBER_EMAIL],
                $params[MemberService::FIELD_MEMBER_FIRST_NAME],
                $params[MemberService::FIELD_MEMBER_DATE_OF_BIRTH]);
            if (!$res['success']) {
                $msg = $res['message'];
                $this->log->debug("Registration failed: $msg", $params);
            } else {
                return $this->registerNewMember($params[MemberService::FIELD_USERNAME],
                    $params[MemberService::FIELD_EMAIL],
                    $params[MemberService::FIELD_PASSWORD],
                    $params[MemberService::FIELD_DATE_OF_BIRTH],
                    $params[MemberService::FIELD_FIRST_NAME],
                    $params[MemberService::FIELD_LAST_NAME]);
            }
        }
        return ['success' => false, 'message' => $msg];
    }

    /**
     * Update a member object with new values and call for update in DB
     * @param member Member
     * @param params [string] : new values submitted by update form
     * @return mixed array('success': bool, 'message':string)
     */
    public function updatePowonMember($member, $params){
        //TODO more validation in JS
        $msg = '';
        if (!Validation::validateParametersExist(
            [
                MemberService::FIELD_EMAIL,
                MemberService::FIELD_FIRST_NAME,
                MemberService::FIELD_LAST_NAME,
                MemberService::FIELD_DATE_OF_BIRTH,
            ], $params)
        ) {
            $msg = 'Invalid parameters entered';
            $this->log->debug("Registration failed: $msg", $params);
        } else {
            $member->setUserEmail($params[MemberService::FIELD_EMAIL]);
            $member->setFirstName($params[MemberService::FIELD_FIRST_NAME]);
            $member->setLastName($params[MemberService::FIELD_LAST_NAME]);
            $member->setDateOfBirth($params[MemberService::FIELD_DATE_OF_BIRTH]);
            if(isset($params[MemberService::FIELD_INTERESTS])){
              $interests = array_map(function($it) {
                   return new Interest(array('interest_name'=>$it));
               }, $params[MemberService::FIELD_INTERESTS]);

              $currentInterests = $this->interestDAO->getInterestsForMember($member->getMemberId());
              foreach ($currentInterests as $key => $value) {
                  if(!in_array($value->getName(), $params[MemberService::FIELD_INTERESTS])){
                      $this->interestDAO->RemoveInterestByNamForMamber($value->getName(), $member->getMemberId());
                  }
              }
              foreach ($interests as $key => $value) {
                  $this->interestDAO->addInterestForMember($value, $member->getMemberId());
              }
            }
            if(isset($params[MemberService::FIELD_PROFESSION_NAME])){
              $workAs = new WorkAs(
                            array(
                                    'member_id' => $member->getMemberId(),
                                    'profession_name' => $params[MemberService::FIELD_PROFESSION_NAME],
                                    'date_started' => $params[MemberService::FIELD_DATE_STARTED],
                                    'date_ended' => $params[MemberService::FIELD_DATE_ENDED],
                                )
                            );
              $this->professionDAO->updateProfessionForMember($workAs);
            }

            if(isset($params[MemberService::FIELD_REGION_COUNTRY])){
              $region = new Region(
                            array(
                                    'region_id' => '',
                                    'country' => $params[MemberService::FIELD_REGION_COUNTRY],
                                    'province' => $params[MemberService::FIELD_REGION_PROVINCE],
                                    'city' => $params[MemberService::FIELD_REGION_CITY],
                                )
                            );
              $this->regionDAO->updateRegionForMember($region, $member->getMemberId());
            }

            return $this->updateMember($member);
        }
        return ['success' => false, 'message' => $msg];
    }

    /**
     * Update a member object with new values and call for update in DB
     * @param member Member
     * @param params [string] : new values submitted by update form
     * @return mixed array('success': bool, 'message':string)
     */
    public function updatePowonMemberAdmin($member, $params){
        $logger = $this->log;
        //$logger->info($params[MemberService::FIELD_IS_ADMIN]);
        $msg = '';

            if(isset($params[MemberService::FIELD_IS_ADMIN]))
            //if(($params[MemberService::FIELD_IS_ADMIN] == 'on'))
            {
                $logger->info("field_is_admin == on");
                $member->setAdmin(true);
            }
            else
            {
                //$logger->info("field_is_admin == something else");
                $member->setAdmin(false);
            }

            if($params[MemberService::FIELD_STATUS]=="A")
            {
                $member->setStatus('A');
            }
            else if($params[MemberService::FIELD_STATUS]=="I")
            {
                $member->setStatus('I');
            }
            else if($params[MemberService::FIELD_STATUS]=="S")
            {
                $member->setStatus('S');
            }

            return $this->updateMember($member);
        }
        //return ['success' => false, 'message' => $msg];

    /**
     * Deletes the member with given member id
     * @param $member_id
     * @return bool true on success, false on failure
     */
    public function deleteMember($member_id)
    {
        try{
            if($this->memberDAO->deleteMember($member_id)){
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting member: ". $ex->getMessage());
        }
        return false;
    }

    /**
     * Update member values in DB
     * @param member Member
     * @return mixed array('success': bool, 'message':string)
     */
    public function updateMember($member){
        //TODO JS form validation, additional validation?
        $update_success = false;
        try{
            $update_success = $this->memberDAO->updateMember($member);
        } catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred when updating a member: ". $ex->getMessage());
        }
        if ($update_success){
            return [
                'success' => true,
                'message' => 'Your profile has been successfully updated'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error updating profile'
            ];
        }
    }

    /**
     * @return Interest[] All the interests
     */
    public function getAllInterests() {
        try {
            return $this->interestDAO->getAllInterests();
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * @return Profession[] All the profession
     */
    public function getAllProfessions() {
        try {
            return $this->professionDAO->getAllProfessions();
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return [];
        }
    }
    //  /**
    //   * @param member Member
    //   */
    public function updateProfilePic($member, $file){
        $mid = $member->getMemberId();
        $target_dir = "assets/images/profile/$mid/";
        $target_file = $target_dir . basename($file->getClientFilename());
        $valid = Validation::validateImageUpload($target_file, $file);
        if ($valid['success']){
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file->moveTo($target_file);
            $member->setProfilePic('/' . $target_file);
            try{
            $this->memberDAO->updateMember($member);
            } catch (\PDOException $ex){
                $this->log->error("A pdo exception occurred when updating a member: $ex->getMessage()");
            }
         }
         return $valid;
     }

    /**
     * @param $id int|string The member id to get
     * @return Member|null
     */
    public function getMemberById($member_id)
    {
        try {
            return $this->memberDAO->getMemberById($member_id);
        } catch (\PDOException $ex) {
            $this->log->error('PDO Exception when getting member by id ' . $member_id . $ex->getMessage());
        }
        return null;
    }

     /**
     * @param Member $auth_member
     * @param array $params
     * @return Member[] of member entities.
     */
    public function searchMembers($auth_member,$params){
        if(empty($params['search_name'])){
            if(isset($params['flag_common_interests']) && $params['flag_common_interests']){
                // Non-empty search_name & checked flag_common_interests
                $auth_member_id = $auth_member->getMemberId();

                try {
                  $interests = $this->interestDAO->getInterestsForMember($auth_member_id);
                }
                catch(\Exception $ex){
                  $this->log->error("An exception occurred when getting interests: ". $ex->getMessage());
                  return [];
                }
                if(sizeof($interests)>0){
                  try{                    
                    return $this->memberDAO->getNewMembersWithInterests($interests);
                  }
                  catch(\Exception $ex){
                    $this->log->error("An exception occurred when getting search results: ". $ex->getMessage());
                    return [];
                  }
                }
                else{ // Authenticated user does not have any interests
                    return [];
                }
            }
            else {
                // Non-empty search_name & unchecked flag_common_interests
                try{
                  return $this->memberDAO->getNewMembers();
                }
                catch(\Exception $ex){
                  $this->log->error("An exception occurred when getting search results: " . $ex->getMessage());
                  return [];
                }
            }
        }
        else {
            $name = $params['search_name'];
            if(isset($params['flag_common_interests']) && $params['flag_common_interests']){
                // Empty search_name & checked flag_common_interests
                $auth_member_id = $auth_member->getMemberId();
                try{
                  $interests = $this->interestDAO->getInterestsForMember($auth_member_id);
                }
                catch(\Exception $ex){
                  $this->log->error("An exception occurred when getting interests: ". $ex->getMessage());
                  return [];
                }
                if(sizeof($interests)>0){
                  try{
                    return $this->memberDAO->searchMembersByNameWithInterests($name,$interests);
                  }
                  catch(\Exception $ex){
                    $this->log->error("An exception occurred when getting search results: ". $ex->getMessage());
                    return [];
                  }
                }
                else{ // Authenticated user does not have any interests
                    return [];
                }
            }
            else {
                // Empty search_name & unchecked flag_common_interests
              try{
                return $this->memberDAO->searchMembersByName($name);
              }
              catch(\Exception $ex){
                $this->log->error("An exception occurred when getting search results: " . $ex->getMessage());
                return [];
              }
            }
        }
    }

    /**
     * @param $member
     * @return mixed
     */
    public function activateStatus($member){

    }
/**
     * @param $member Member entity
     * @param $requester Member who request the password change
     * @param $params array Post request parameters (password1, password2 and password (for the old password)
     * @return array ['success' => bool, 'message' => string]
     */
    public function updatePassword($member, $requester, $params)
    {
        $adminChange = false; // indicates that an admin is changing somebody else's password.
        if ($member->getMemberId() !== $requester->getMemberId()) {
            $adminChange = true;
        }
        $params_to_validate = [MemberService::FIELD_PASSWORD1, MemberService::FIELD_PASSWORD2];
        if (!$adminChange) {
            $params_to_validate[] = MemberService::FIELD_PASSWORD;
        }
        if (!Validation::validateParametersExist($params_to_validate, $params)) {
            return ['success' => false, 'message' => 'You must provide all the required fields.'];
        }
        $this->log->debug("Attempting to update ". $member->getUsername() . "'s password...");
        $new_pwd1 = $params[MemberService::FIELD_PASSWORD1];
        $new_pwd2 = $params[MemberService::FIELD_PASSWORD2];
        if ($new_pwd1 !== $new_pwd2) {
            return ['success' => false, 'message' => 'Passwords don\'t match!'];
        }
        $member = $this->memberDAO->getMemberByUsername($member->getUsername(), true);
        if (!$member) {
            $this->log->error('Changing passwords: member does not exist anymore?', $member->toObject());
            return ['success' => false, 'message' => 'Member does not exist anymore?'];
        }
        if (!$adminChange) { //verify old password if it's not an administrative password change for another user
            $old_pwd = $params[MemberService::FIELD_PASSWORD];
            if (!password_verify($old_pwd, $member->getHashedPassword())) {
                return ['success' => false, 'message' => 'Invalid password.'];
            }
        }
        $new_pwd1 = password_hash($new_pwd1, PASSWORD_BCRYPT);
        try {
            if ($this->memberDAO->updatePassword($member->getMemberId(), $new_pwd1)) {
                $this->log->info("Member ". $member->getUsername() . " updated their password.");
                return ['success' => true, 'message' => 'Password was updated successfully.'];
            }
        } catch (\PDOException $ex) {
            $this->log->error("Could not update ". $member->getUsername(). "'s password. ". $ex->getMessage());
        }
        return ['success' => false, 'message' => 'Something went wrong while updating the password!'];
    }
}
