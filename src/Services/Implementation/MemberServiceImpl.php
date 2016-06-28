<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Member;
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
    }

    /**
     * @return Member[] All the members
     */
    public function getAllMembers() {
        return $this->memberDAO->getAllMembers();
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
            return array('success'=> false, 'message' =>'username exists');
        }
        if ($this->memberDAO->getMemberByEmail($user_email)) {
            $this->log->debug("Email $user_email already exists in the system");
            return array('success'=> false, 'message' =>'Email exists');
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
        if ($this->memberDAO->createNewMember($newMember, $pwd_hash)) {
            $this->log->info('Registered new member',
                ['username' => $username, 'email' => $user_email]);
            return array('success' => true,
                'message' => "New member $username was registered.");
        }
        return array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }
}