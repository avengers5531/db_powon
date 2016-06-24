<?php

use PHPUnit\Framework\TestCase;
use Powon\Dao\MemberDAO;
use Powon\Entity\Member;


class MemberDaoStub implements MemberDAO {
    
    private $members = array(
        [
            'member_id' => 1,
            'username' => 'User1',
            'first_name' => 'First',
            'last_name' => 'Last',
            'user_email' => 'test_user1@mail.ca',
            'date_of_birth' => '1989-12-13'
        ],
        [
            'member_id' => 2,
            'username' => 'User2',
            'first_name' => 'First2',
            'last_name' => 'Last2',
            'user_email' => 'test_user2@mail.ca',
            'date_of_birth' => '1994-02-11'
        ]);

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
}


class MemberServiceImplTest extends TestCase 
{
    // WIP TODO
}
