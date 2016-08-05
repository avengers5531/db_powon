<?php

use PHPUnit\Framework\TestCase;
use Powon\Services\MemberService;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\MemberDAOStub;


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
                'password' => password_hash('User1', PASSWORD_BCRYPT),
                'profile_picture' => '/assets/images/profile/lionfish.jpg',
                'status' => 'A',
                'has_interests' => array(
                    [
                        'interest_name' => 'Fishing',
                        'member_id' => 1
                    ],
                    [
                        'interest_name' => 'Aliens',
                        'member_id' => 1
                    ]
                )
            ],
            [
                'member_id' => 2,
                'username' => 'User2',
                'first_name' => 'First2',
                'last_name' => 'Last2',
                'user_email' => 'test_user2@mail.ca',
                'date_of_birth' => '1994-02-11',
                'is_admin' => 'N',
                'password' => password_hash('User2', PASSWORD_BCRYPT),
                'profile_picture' => '/assets/images/profile/lionfish.jpg',
                'status' => 'A',
                'has_interests' => array(
                    [
                        'interest_name' => 'Basketball',
                        'member_id' => 2
                    ],
                    [
                        'interest_name' => 'Aliens',
                        'member_id' => 2
                    ]
                )
            ],
            [
                'member_id' => 3,
                'username' => 'User3',
                'first_name' => 'First',
                'last_name' => 'Last3',
                'user_email' => 'test_user3@mail.ca',
                'date_of_birth' => '1992-07-26',
                'is_admin' => 'Y',
                'password' => password_hash('User3', PASSWORD_BCRYPT),
                'profile_picture' => '/assets/images/profile/3/fish.jpg',
                'status' => 'A',
                'has_interests' => []
            ],
            );

        $logger = new LoggerStub();

        $interestDAO = new \Powon\Test\Stub\InterestDAOStub();
        $profession = new \Powon\Test\Stub\ProfessionDAOStub();
        $region = new \Powon\Test\Stub\RegionDAOStub();
        //TODO populate this stub 

        $this->memberService = new \Powon\Services\Implementation\MemberServiceImpl($logger,$dao, $interestDAO, $profession, $region);

        $interestDAO->interests = array(
            ['interest_name'=>'Fishing'],
            ['interest_name'=>'Aliens'],
            ['interest_name'=>'Basketball']
        );

        $new_interest = $interestDAO->getInterestByName("Fishing");
        $temp_member = $this->memberService->getMemberByUsername('User1');
        $interestDAO->addInterestForMember($new_interest,$temp_member);

        $new_interest = $interestDAO->getInterestByName("Aliens");
        $temp_member = $this->memberService->getMemberByUsername('User1');
        $interestDAO->addInterestForMember($new_interest,$temp_member);

        $new_interest = $interestDAO->getInterestByName("Basketball");
        $temp_member = $this->memberService->getMemberByUsername('User2');
        $interestDAO->addInterestForMember($new_interest,$temp_member);

        $new_interest = $interestDAO->getInterestByName("Aliens");
        $temp_member = $this->memberService->getMemberByUsername('User2');
        $interestDAO->addInterestForMember($new_interest,$temp_member);
    }

    public function testGetAllMembers() {
        $res = $this->memberService->getAllMembers();
        $this->assertEquals(count($res), 3);
    }

    public function testRegisterNewMember() {

        $res = $this->memberService->registerNewMember(
            'User1', 'test_user3@mail.ca' , 'Lalala' , '1984-04-01' , 'First3' , 'Last3'
        );
        $this->assertFalse($res['success']);
        $this->assertEquals($res['message'], 'Username exists.');

        $res = $this->memberService->registerNewMember(
            'NewUser3', 'test_user1@mail.ca' , 'Lalala' , '1984-04-01' , 'First3' , 'Last3'
        );
        $this->assertFalse($res['success']);
        $this->assertEquals($res['message'], 'Email exists.');

        $res = $this->memberService->registerNewMember(
            'NewUser3', 'test_new_user3@mail.ca' , 'Lalala' , '1984-04-01' , 'First3' , 'Last3'
        );
        $this->assertTrue($res['success']);

        $res = $this->memberService->getAllMembers();
        $this->assertEquals(count($res), 4);
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

    public function testInterestPowonMember() {
        $member = $this->memberService->getMemberByUsername('User2');
        $params = array('user_email' => 'test_user2@mail.ca',
                        'first_name' => 'Something',
                        'last_name' => 'Else',
                        'date_of_birth' => '1994-02-11');
        $params[MemberService::FIELD_INTERESTS] = array('Aliens','Fishing');

        $message = $this->memberService->updatePowonMember($member, $params);
        $this->assertEquals($message['success'], true);

        $interest = $this->memberService->populateInterestsForMember($member);
        $member_interest = $member->getInterests();

        $this->assertEquals($member_interest[0]->getName(), 'Aliens');
        $this->assertEquals($member_interest[1]->getName(), 'Fishing');
    }
    /* FIXME
    public function testProfessionPowonMember() {
        $member = $this->memberService->getMemberByUsername('User2');
        $params = array('user_email' => 'test_user2@mail.ca',
                        'first_name' => 'Something',
                        'last_name' => 'Else',
                        'date_of_birth' => '1994-02-11');
        $params[MemberService::FIELD_PROFESSION_NAME] = 'Student';
        $params[MemberService::FIELD_DATE_STARTED] = '2014-1-1';
        $params[MemberService::FIELD_DATE_ENDED] = '2016-12-1';

        $message = $this->memberService->updatePowonMember($member, $params);
        $this->assertEquals($message['success'], true);

        $member = $this->memberService->populateProfessionForMember($member);

        $this->assertEquals($member->getProfession_name(), 'Student');
        $this->assertEquals($member->getProfession_date_started(), '2014-1-1');
        $this->assertEquals($member->getProfession_date_ended(), '2016-12-1');
    }
    */
    public function testRegionPowonMember() {
        $member = $this->memberService->getMemberByUsername('User2');
        $params = array('user_email' => 'test_user2@mail.ca',
                        'first_name' => 'Something',
                        'last_name' => 'Else',
                        'date_of_birth' => '1994-02-11');
        $params[MemberService::FIELD_REGION_COUNTRY] = 'Canada';
        $params[MemberService::FIELD_REGION_PROVINCE] = 'Quebec';
        $params[MemberService::FIELD_REGION_CITY] = 'Montreal';

        $message = $this->memberService->updatePowonMember($member, $params);
        $this->assertEquals($message['success'], true);

        $member = $this->memberService->populateRegionForMember($member);
        
        $member_region = $member->getRegion();

        $this->assertEquals($member_region->getRegionCountry(), 'Canada');
        $this->assertEquals($member_region->getRegionProvince(), 'Quebec');
        $this->assertEquals($member_region->getRegionCity(), 'Montreal');
    }

    public function testSearchMembers(){
        $auth_member =  $this->memberService->getMemberByUsername('User1');
        $params = array(
            'search_name' => "",
            'flag_common_interests' => true
        );

        $res = $this->memberService->searchMembers($auth_member,$params);

        $this->assertEquals(0,count($res));
    }

    public function testUpdatePasswordSuccess() {
        $user1 = $this->memberService->getMemberById(1);
        $params = [
            MemberService::FIELD_PASSWORD => 'User1',
            MemberService::FIELD_PASSWORD1 => 'newUser1',
            MemberService::FIELD_PASSWORD2 => 'newUser1'
        ];
        $res = $this->memberService->updatePassword($user1, $user1, $params);
        $this->assertTrue($res['success']);

        // put it back
        $params = [
            MemberService::FIELD_PASSWORD => 'newUser1',
            MemberService::FIELD_PASSWORD1 => 'User1',
            MemberService::FIELD_PASSWORD2 => 'User1'
        ];
        $res = $this->memberService->updatePassword($user1, $user1, $params);
        $this->assertTrue($res['success']);
    }

    public function testUpdatePasswordFail() {
        // admin updates their own password without specifying old pwd.
        $admin = $this->memberService->getMemberById(3);
        $params = [
            MemberService::FIELD_PASSWORD1 => 'newUser3',
            MemberService::FIELD_PASSWORD2 => 'newUser3'
        ];
        $res = $this->memberService->updatePassword($admin, $admin, $params);
        $this->assertFalse($res['success']);

        $user2 = $this->memberService->getMemberById(2);
        $params = [
            MemberService::FIELD_PASSWORD => 'User2',
            MemberService::FIELD_PASSWORD1 => 'User2a',
            MemberService::FIELD_PASSWORD2 => 'User2b'
        ];
        $res = $this->memberService->updatePassword($user2, $user2, $params);
        $this->assertFalse($res['success']);

        unset($params[MemberService::FIELD_PASSWORD]);
        $res = $this->memberService->updatePassword($user2, $admin, $params);
        $this->assertFalse($res['success']);

        // user with bad old password
        $params = [
            MemberService::FIELD_PASSWORD => 'User2a',
            MemberService::FIELD_PASSWORD1 => 'newUser2',
            MemberService::FIELD_PASSWORD2 => 'newUser2'
        ];
        $res = $this->memberService->updatePassword($user2, $user2, $params);
        $this->assertFalse($res['success']);

        // fix it
        $params[MemberService::FIELD_PASSWORD] = 'User2';
        $res = $this->memberService->updatePassword($user2, $user2, $params);
        $this->assertTrue($res['success']);
    }

    public function testUpdatePasswordAdmin() {
        $admin = $this->memberService->getMemberById(3);
        $params = [
            MemberService::FIELD_PASSWORD1 => 'newUser3',
            MemberService::FIELD_PASSWORD2 => 'newUser3',
            MemberService::FIELD_PASSWORD => 'User3'
        ];
        $res = $this->memberService->updatePassword($admin, $admin, $params);
        $this->assertTrue($res['success']);
        $user2 = $this->memberService->getMemberById(2);
        unset($params[MemberService::FIELD_PASSWORD]);

        $res = $this->memberService->updatePassword($user2, $admin, $params);
        $this->assertTrue($res['success']);
    }

    // Add more tests as MemberService grows

}
