<?php

use phpunit\framework\TestCase;

// TODO remove these require and use the powon_autoload.php
require_once '../../src/Dao/MemberDAO.php';
require_once '../../src/Entity/Member.php';

class MemberDaoStub implements MemberDAO {

    public function getAllMembers()
    {
        $results = array(
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
        return array_map(function($data) {
            return new Member($data);
        }, $results);
    }
}


class MemberServiceImplTest extends TestCase 
{
    // WIP TODO
}
