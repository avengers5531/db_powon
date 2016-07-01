<?php

namespace Powon\Test\unit;


use Powon\Services\Implementation\SessionServiceImpl;
use Powon\Services\SessionService;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\MemberDaoStub;
use Powon\Test\Stub\SessionDAOStub;

class SessionServiceImplTest extends \PHPUnit_Framework_TestCase
{

    private $memberDAO;

    private $sessionDAO;

    private $logger;

    /**
     * @var SessionService
     */
    private $sessionService;

    function setUp()
    {
        parent::setUp();
        $this->memberDAO = new MemberDaoStub();
        $this->sessionDAO = new SessionDAOStub();
        $this->logger = new LoggerStub();
        $this->memberDAO->members = array(
            [
                'member_id' => 1,
                'username' => 'User1',
                'first_name' => 'First',
                'last_name' => 'Last',
                'user_email' => 'test_user1@mail.ca',
                'date_of_birth' => '1989-12-13',
                'password' => password_hash('Boo', PASSWORD_BCRYPT),
                'is_admin' => 'N'
            ],
            [
                'member_id' => 2,
                'username' => 'User2',
                'first_name' => 'First2',
                'last_name' => 'Last2',
                'user_email' => 'test_user2@mail.ca',
                'date_of_birth' => '1994-02-11',
                'password' => password_hash('Aha', PASSWORD_BCRYPT),
                'is_admin' => 'Y'
            ]);

        $this->sessionService = new SessionServiceImpl($this->logger, $this->memberDAO, $this->sessionDAO);
    }

    function testCreateSession() {
        $res = $this->sessionService->authenticateUserByEmail('test_user2@mail.ca', 'Aha');
        $this->assertTrue($res);
        $this->assertTrue($this->sessionService->isAuthenticated() && $this->sessionService->isAdmin());
        $admin = $this->sessionService->getAuthenticatedMember();
        $this->assertEquals($admin->getMemberId(), 2);
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_ACTIVE);
    }
    
    // TODO more tests

}
