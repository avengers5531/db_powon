<?php

use PHPUnit\Framework\TestCase;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\MemberDaoStub;


class MemberServiceImplTest extends TestCase 
{

    /**
     * @var \Powon\Services\MemberService $memberService
     */
    private $memberService;
    
    public function setUp()
    {
        parent::setUp();
        $dao = new MemberDaoStub();
        $dao->members = array(
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
        $logger = new LoggerStub();
        $this->memberService = new \Powon\Services\Implementation\MemberServiceImpl($logger,$dao);
    }

    public function testGetAllMembers() {
        $res = $this->memberService->getAllMembers();
        $this->assertEquals(count($res), 2);
    }
    
    // TODO more tests

}
