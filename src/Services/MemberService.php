<?php

namespace Powon\Services;

use Powon\Entity\Member;

interface MemberService {

    // Constants for the registration forms
    const FIELD_USERNAME = 'username';
    const FIELD_PASSWORD = 'password';
    const FIELD_PASSWORD2 = 'password2';
    const FIELD_FIRST_NAME = 'first_name';
    const FIELD_LAST_NAME = 'last_name';
    const FIELD_EMAIL = 'user_email';
    const FIELD_DATE_OF_BIRTH = 'date_of_birth';
    // existing member details for validation
    const FIELD_MEMBER_EMAIL = 'member_email';
    const FIELD_MEMBER_FIRST_NAME = 'member_first_name';
    const FIELD_MEMBER_DATE_OF_BIRTH = 'member_dob';


    /**
     * @return Member[] All the members
     */
    public function getAllMembers();

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
                                      $last_name);

    /**
     * Checks whether a member with the given email address, first name and date of birth exists.
     * @param $email
     * @param $first_name
     * @param $date_of_birth
     * @return bool True on success, false otherwise
     */
    public function doesMemberExist($email,
                                    $first_name,
                                    $date_of_birth);

    /**
     * Use this method to register a new member to the Powon system.
     * Unlike the registerNewMember method, this one complies
     * with the specifications and checks for an existing member first.
     * @param $params [string] the parameters from the request
     * @return mixed array('success': bool, 'message':string)
     */
    public function registerPowonMember($params);

    /**
     * Return the Member entity with the given username
     * @param $username [string] the member's username
     */
     public function getMemberByUsername($username);
}
