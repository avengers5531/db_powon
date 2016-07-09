<?php

use PHPUnit\Framework\TestCase;
use Powon\Services\MemberService;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\MemberDaoStub;


class MemberServiceImplTest extends TestCase 
{

    /**
     * @var MemberService $memberService
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
        $this->memberService = new \Powon\Services\Implementation\MemberServiceImpl($logger,$dao);
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
    
    public function testDoesMemberExist()
    {
        $res = $this->memberService->doesMemberExist('test_user1@mail.ca', 'First' , '1989-12-13');
        $this->assertTrue($res);

        $res = $this->memberService->doesMemberExist('test_user1@mail.ca', 'First' , '1989-12-14');
        $this->assertFalse($res);

        $res = $this->memberService->doesMemberExist('test_user1@mail.ca', 'First2' , '1989-12-13');
        $this->assertFalse($res);

        $res = $this->memberService->doesMemberExist('test_user2@mail.ca', 'First' , '1989-12-13');
        $this->assertFalse($res);

        $res = $this->memberService->doesMemberExist('test_user4@mail.ca', 'First' , '1989-12-13');
        $this->assertFalse($res);
    }

    public function testRegisterPowonMember() {
        // valid request
        $requestParams = [
            MemberService::FIELD_USERNAME => 'testuser',
            MemberService::FIELD_EMAIL => 'testUser@powon.ca',
            MemberService::FIELD_PASSWORD => 'pwd',
            MemberService::FIELD_FIRST_NAME => 'First Name',
            MemberService::FIELD_LAST_NAME => 'Last Name',
            MemberService::FIELD_DATE_OF_BIRTH => '1993-08-02',
            MemberService::FIELD_MEMBER_EMAIL => 'test_user1@mail.ca',
            MemberService::FIELD_MEMBER_FIRST_NAME => 'First',
            MemberService::FIELD_MEMBER_DATE_OF_BIRTH => '1989-12-13'
        ];

        $res = $this->memberService->registerPowonMember($requestParams);
        $this->assertTrue($res['success']);

        // register again
        $res = $this->memberService->registerPowonMember($requestParams);
        $this->assertFalse($res['success']);

        // change to an invalid request.
        $requestParams[MemberService::FIELD_USERNAME] = 'testuser2';
        $requestParams[MemberService::FIELD_EMAIL] = 'testuser2@mail.ca';
        $requestParams[MemberService::FIELD_MEMBER_FIRST_NAME] = 'First1'; // bad
        $res = $this->memberService->registerPowonMember($requestParams);
        $this->assertFalse($res['success']);

        // missing fields
        $requestParams[MemberService::FIELD_DATE_OF_BIRTH] = '';
        $requestParams[MemberService::FIELD_MEMBER_FIRST_NAME] = 'First';
        $res = $this->memberService->registerPowonMember($requestParams);
        $this->assertFalse($res['success']);

        //put it the date back
        $requestParams[MemberService::FIELD_DATE_OF_BIRTH] = '1992-09-02';
        $requestParams[MemberService::FIELD_MEMBER_FIRST_NAME] = 'First';
        $res = $this->memberService->registerPowonMember($requestParams);
        $this->assertTrue($res['success']);

    }

    // Add more tests as MemberService grows

}
