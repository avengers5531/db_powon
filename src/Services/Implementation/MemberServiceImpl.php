<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Member;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;
use Powon\Services\MemberService;
use Powon\Dao\MemberDAO;
use Powon\Utils\DateTimeHelper;

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

    public function __construct(LoggerInterface $logger, MemberDAO $dao)
    {
        $this->memberDAO = $dao;
        $this->log = $logger;
    }

    /**
     * @return Member[] All the members
     */
    public function getAllMembers() {
        try {
            return $this->memberDAO->getAllMembers();
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
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
         $this->log->error("A pdo exception occured: $ex->getMessage()");
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
            'date_of_birth' => $date_of_birth
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
            $this->log->error("A pdo exception occurred when registering a new user: " . $ex->getMessage());
        }
        return array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
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
            " email $email exists: $ex->getMessage()");
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
                MemberService::FIELD_DATE_OF_BIRTH
            ], $params)
        ) {
            $msg = 'Invalid parameters entered';
            $this->log->debug("Registration failed: $msg", $params);
        } else {
            $member->setUserEmail($params[MemberService::FIELD_EMAIL]);
            $member->setFirstName($params[MemberService::FIELD_FIRST_NAME]);
            $member->setLastName($params[MemberService::FIELD_LAST_NAME]);
            $member->setDateOfBirth($params[MemberService::FIELD_DATE_OF_BIRTH]);
            return $this->updateMember($member);
        }
        return ['success' => false, 'message' => $msg];
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
            $this->log->error("A pdo exception occurred when updating a member: $ex->getMessage()");
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
}
