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
                'is_admin' => 'N',
                'profile_picture' => '/assets/images/profile/lionfish.jpg'
            ],
            [
                'member_id' => 2,
                'username' => 'User2',
                'first_name' => 'First2',
                'last_name' => 'Last2',
                'user_email' => 'test_user2@mail.ca',
                'date_of_birth' => '1994-02-11',
                'is_admin' => 'N',
                'profile_picture' => '/assets/images/profile/lionfish.jpg'
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
        $this->assertTrue($res['success']);

        $res = $this->memberService->doesMemberExist('test_user1@mail.ca', 'First' , '1989-12-14');
        $this->assertFalse($res['success']);

        $res = $this->memberService->doesMemberExist('test_user1@mail.ca', 'First2' , '1989-12-13');
        $this->assertFalse($res['success']);

        $res = $this->memberService->doesMemberExist('test_user2@mail.ca', 'First' , '1989-12-13');
        $this->assertFalse($res['success']);

        $res = $this->memberService->doesMemberExist('test_user4@mail.ca', 'First' , '1989-12-13');
        $this->assertFalse($res['success']);
    }

    public function testRegisterPowonMember() {
        // invalid request (missing password2)
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
        $this->assertFalse($res['success']);

        // not matching passwords.
        $requestParams[MemberService::FIELD_PASSWORD2] = 'wrong_pwd';
        $res = $this->memberService->registerPowonMember($requestParams);
        $this->assertFalse($res['success']);

        // good request
        $requestParams[MemberService::FIELD_PASSWORD2] = 'pwd';
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

    public function testGetMemberByUsername(){
        $member = $this->memberService->getMemberByUsername('User2');
        $this->assertEquals($member->getMemberId(), 2);
        $this->assertEquals($member->getUsername(), 'User2');
        $this->assertEquals($member->getFirstName(), 'First2');
        $this->assertEquals($member->getLastName(), 'Last2');
        $this->assertEquals($member->getUserEmail(), 'test_user2@mail.ca');
        $this->assertEquals($member->getDateOfBirth(), '1994-02-11');
    }


    public function testUpdateMember(){
        $member = $this->memberService->getMemberByUsername('User2');
        $member->setFirstName("NewFName");
        $member->setLastName("NewLName");
        $message = $this->memberService->updateMember($member);
        $this->assertEquals($message['success'], true);
        $updated_member = $this->memberService->getMemberByUsername('User2');
        $this->assertEquals($updated_member->getFirstName(), 'NewFName');
        $this->assertEquals($updated_member->getLastName(), 'NewLName');
        $updated_member->setFirstName("First2");
        $updated_member->setLastName("Last2");
        $this->memberService->updateMember($updated_member);
        $this->assertEquals($updated_member->getFirstName(), 'First2');
        $this->assertEquals($updated_member->getLastName(), 'Last2');
    }

    public function testUpdatePowonMember(){
        $member = $this->memberService->getMemberByUsername('User2');
        $params = array('user_email' => 'test_user2@mail.ca',
                        'first_name' => 'Something',
                        'last_name' => 'Else',
                        'date_of_birth' => '1994-02-11');
        $message = $this->memberService->updatePowonMember($member, $params);
        $this->assertEquals($message['success'], true);
        $updated_member = $this->memberService->getMemberByUsername('User2');
        $this->assertEquals($updated_member->getFirstName(), 'Something');
        $this->assertEquals($updated_member->getLastName(), 'Else');
        $this->assertEquals($updated_member->getUserEmail(), 'test_user2@mail.ca');
        $this->assertEquals($updated_member->getDateOfBirth(), '1994-02-11');
        $updated_member->setFirstName("First2");
        $updated_member->setLastName("Last2");
        $this->memberService->updateMember($updated_member);
        $this->assertEquals($updated_member->getFirstName(), 'First2');
        $this->assertEquals($updated_member->getLastName(), 'Last2');
    }

    // Add more tests as MemberService grows

}
