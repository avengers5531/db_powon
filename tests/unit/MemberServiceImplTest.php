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
                'date_of_birth' => '1989-12-13',
                'is_admin' => 'N'
            ],
            [
                'member_id' => 2,
                'username' => 'User2',
                'first_name' => 'First2',
                'last_name' => 'Last2',
                'user_email' => 'test_user2@mail.ca',
                'date_of_birth' => '1994-02-11',
                'is_admin' => 'N'
            ]);
        $logger = new LoggerStub();

        $interestDAO = new \Powon\Test\Stub\InterestDAOStub();

        $profession = new \Powon\Test\Stub\ProfessionDAOStub();
        $region = new \Powon\Test\Stub\RegionDAOStub();
        //TODO populate this stub 

        $this->memberService = new \Powon\Services\Implementation\MemberServiceImpl($logger,$dao, $interestDAO, $profession, $region);

    }

    public function testGetAllMembers() {
        $res = $this->memberService->getAllMembers();
        $this->assertEquals(count($res), 2);
    }
    
    public function testRegisterNewMember() {
        
        $res = $this->memberService->registerNewMember(
            'User1', 'test_user3@mail.ca' , 'Lalala' , '1984-04-01' , 'First3' , 'Last3'
        );
        $this->assertFalse($res['success']);
        $this->assertEquals($res['message'], 'Username exists.');

        $res = $this->memberService->registerNewMember(
            'User3', 'test_user1@mail.ca' , 'Lalala' , '1984-04-01' , 'First3' , 'Last3'
        );
        $this->assertFalse($res['success']);
        $this->assertEquals($res['message'], 'Email exists.');

        $res = $this->memberService->registerNewMember(
            'User3', 'test_user3@mail.ca' , 'Lalala' , '1984-04-01' , 'First3' , 'Last3'
        );
        $this->assertTrue($res['success']);

        $res = $this->memberService->getAllMembers();
        $this->assertEquals(count($res), 3);
    }
    
    // Add more tests as MemberService grows

}
