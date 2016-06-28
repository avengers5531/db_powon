<?php

namespace Powon\Test\Stub;

use Powon\Dao\MemberDAO;
use Powon\Entity\Member;

class MemberDaoStub implements MemberDAO {

    /**
     * @var array of mock member data.
     */
    public $members;

    public function getAllMembers()
    {
        $results = $this->members;
        return array_map(function($data) {
            return new Member($data);
        }, $results);
    }

    /**
     * @param int $id
     * @return Member|null
     */
    public function getMemberById($id)
    {
        // TODO: Implement getMemberById() method.
        return null;
    }

    /**
     * @param string $username
     * @param bool $withPwd Set to true if you want the hashed password of the user.
     * @return Member|null
     */
    public function getMemberByUsername($username, $withPwd = false)
    {
        // TODO: Implement getMemberByUsername() method.
        return null;
    }

    /**
     * @param string $email
     * @param bool $withPwd set to true if you want the hashed password.
     * @return Member|null
     */
    public function getMemberByEmail($email, $withPwd = false)
    {
        // TODO: Implement getMemberByEmail() method.
        return null;
    }

    /**
     * @param $entity Member
     * @param $hashed_pwd string
     * @return bool
     */
    public function createNewMember($entity, $hashed_pwd)
    {
        // TODO: Implement createNewMember() method.
        return false;
    }
}