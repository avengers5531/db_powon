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

    function helperCreateSessionService() {
        return new SessionServiceImpl($this->logger, $this->memberDAO, $this->sessionDAO);
    }

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

        $this->sessionService = $this->helperCreateSessionService();
    }

    function testCreateSession() {
        // wrong password
        $res = $this->sessionService->authenticateUserByUsername('User1', 'Aha');
        $this->assertFalse($res);
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_DOES_NOT_EXIST);
        $this->assertFalse($this->sessionService->isAuthenticated());
        $this->assertNull($this->sessionService->getAuthenticatedMember());
        $this->assertNull($this->sessionService->getSession());

        //correct password, remember me false.
        $res = $this->sessionService->authenticateUserByEmail('test_user2@mail.ca', 'Aha');
        $this->assertTrue($res);
        $this->assertTrue($this->sessionService->isAuthenticated() && $this->sessionService->isAdmin());
        $admin = $this->sessionService->getAuthenticatedMember();
        $this->assertEquals($admin->getMemberId(), 2);
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_ACTIVE);
        $this->assertFalse($this->sessionService->getSession()->getSessionData()['remember']);

        // logout and log back in:
        $this->assertTrue($this->sessionService->destroyAllSessions());
        //correct password, remember me true.
        $res = $this->sessionService->authenticateUserByEmail('test_user2@mail.ca', 'Aha', true);
        $this->assertTrue($res);
        $this->assertTrue($this->sessionService->isAuthenticated() && $this->sessionService->isAdmin());
        $admin = $this->sessionService->getAuthenticatedMember();
        $this->assertEquals($admin->getMemberId(), 2);
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_ACTIVE);
        $this->assertTrue($this->sessionService->getSession()->getSessionData()['remember']);
    }

    function testLoadSession() {
        // random token
        $res = $this->sessionService->loadSession('some_random_token');
        $this->assertEquals($res, SessionService::SESSION_DOES_NOT_EXIST);
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_DOES_NOT_EXIST);

        // create a session and get token
        $this->sessionService->authenticateUserByUsername('User1', 'Boo');
        $token = $this->sessionService->getSession()->getToken();

        // simulate a new request. Object gets reset.
        $this->sessionService = $this->helperCreateSessionService();


        $this->assertEquals($this->sessionService->loadSession($token), SessionService::SESSION_ACTIVE);
        $this->assertTrue($this->sessionService->isAuthenticated() && !$this->sessionService->isAdmin());
        $member = $this->sessionService->getAuthenticatedMember();
        $this->assertEquals($member->getMemberId(), 1);
    }

    function testSessionExpiry() {
        // create a session
        $this->sessionService->authenticateUserByUsername('User1', 'Boo');
        $token = $this->sessionService->getSession()->getToken();

        //sleep for 1 second so that session expires
        sleep(1);

        // simulate a new request.
        $this->sessionService = $this->helperCreateSessionService();
        //set expiry to 1 second.
        $this->sessionService->setExpiration(1);
        $this->assertEquals($this->sessionService->getTokenValidityPeriod(), 1);

        //session should be expired now
        $res = $this->sessionService->loadSession($token);
        $this->assertEquals($res, SessionService::SESSION_EXPIRED);
        $this->assertFalse($this->sessionService->isAuthenticated());
        $this->assertNull($this->sessionService->getAuthenticatedMember());
        $this->assertNull($this->sessionService->getSession());

        // should not be saved by the next request
        $this->sessionService = $this->helperCreateSessionService();
        $this->assertEquals($this->sessionService->loadSession($token), SessionService::SESSION_DOES_NOT_EXIST);
    }

    function testSessionDestroy() {
        // create a session
        $this->sessionService->authenticateUserByUsername('User2', 'Aha');
        $token = $this->sessionService->getSession()->getToken();

        // Simulate a new request, but create a new session.
        $this->sessionService = $this->helperCreateSessionService();

        $this->sessionService->authenticateUserByEmail('test_user2@mail.ca', 'Aha');
        $token2 = $this->sessionService->getSession()->getToken();

        $this->sessionService = $this->helperCreateSessionService();

        // use the first token
        $this->sessionService->loadSession($token);
        $this->assertEquals($this->sessionService->getAuthenticatedMember()->getMemberId(), 2);

        //destroy the session
        $this->sessionService->destroySession();
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_ENDED);
        $this->assertFalse($this->sessionService->isAuthenticated());
        $this->assertNull($this->sessionService->getAuthenticatedMember());
        $this->assertNull($this->sessionService->getSession());

        $this->assertEquals($this->sessionService->loadSession($token), SessionService::SESSION_DOES_NOT_EXIST);

        //ensure the second token still works.
        $this->sessionService = $this->helperCreateSessionService();
        $this->assertEquals($this->sessionService->loadSession($token2), SessionService::SESSION_ACTIVE);
        $this->assertEquals($this->sessionService->getAuthenticatedMember()->getMemberId(), 2);
    }

    function testSessionDestroyAll() {
        // create multiple sessions
        $this->sessionService->authenticateUserByUsername('User2', 'Aha');
        $token1 = $this->sessionService->getSession()->getToken();

        // simulate a new request.
        $this->sessionService = $this->helperCreateSessionService();
        $this->sessionService->authenticateUserByUsername('User2', 'Aha');
        $token2 = $this->sessionService->getSession()->getToken();

        // simulate a new request.
        $this->sessionService = $this->helperCreateSessionService();
        $this->sessionService->authenticateUserByUsername('User2', 'Aha');
        $token3 = $this->sessionService->getSession()->getToken();

        // now destroy all the sessions
        $this->sessionService->loadSession($token1);
        $this->assertEquals($this->sessionService->getAuthenticatedMember()->getMemberId(), 2);

        $this->sessionService->destroyAllSessions();
        $this->assertEquals($this->sessionService->getSessionState(), SessionService::SESSION_ENDED);
        $this->assertFalse($this->sessionService->isAuthenticated());
        $this->assertNull($this->sessionService->getAuthenticatedMember());
        $this->assertNull($this->sessionService->getSession());

        $this->sessionService = $this->helperCreateSessionService();
        $this->assertEquals($this->sessionService->loadSession($token1), SessionService::SESSION_DOES_NOT_EXIST);
        $this->assertEquals($this->sessionService->loadSession($token2), SessionService::SESSION_DOES_NOT_EXIST);
        $this->assertEquals($this->sessionService->loadSession($token3), SessionService::SESSION_DOES_NOT_EXIST);
    }


}
