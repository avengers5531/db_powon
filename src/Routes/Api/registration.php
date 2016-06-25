<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/v1/login', function (Request $request, Response $response) {
    $username = $request->getHeader('username');
    $password = $request->getHeader('password');
    if ($username && isset($username[0]) && $password && isset($password[0])) {
        $username = $username[0];
        $password = $password[0];
        /**
         * @var \Powon\Services\SessionService $sessionService
         */
        $sessionService = $this->sessionService;
        $this->logger->debug("Got request with username: $username");
        if ($sessionService->authenticateUserByUsername($username,$password)) {
            $responseObj = array(
                'status' => 'success',
                'message' => 'Login was successful!'
            );
            $body = $response->getBody();
            $body->write(json_encode($responseObj));
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
    }
    $responseObj = array(
        'status' => 'failure',
        'message' => 'Invalid username/password combination.'
    );
    $body = $response->getBody();
    $body->write(json_encode($responseObj));
    return $response->withStatus(403, 'Forbidden')
        ->withHeader('Content-Type', 'application/json');
});

$app->get('/api/v1/logout', function (Request $request, Response $response) {
    /**
     * @var \Powon\Services\SessionService $sessionService
     */
    $sessionService = $this->sessionService;

    if ($sessionService->isAuthenticated() &&
        $sessionService->destroySession()) {
        $responseObj = array(
            'status' => 'success',
            'message' => 'Logged out successfully.'
        );
        $response->getBody()->write(json_encode($responseObj));
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json');
    }
    if (!$sessionService->isAuthenticated()) {
        return $response->withStatus(403, 'Forbidden');
    }
    $responseObj = array(
        'status' => 'failure',
        'message' => 'something went wrong...'
    );
    $response->getBody()->write(json_encode($responseObj));
    return $response->withStatus(500)
        ->withHeader('Content-Type','application/json' );

});

$app->get('/api/v1/members', function (Request $request, Response $response) {
    /**
     * @var $logger \Psr\Log\LoggerInterface
     */
    $logger = $this->logger;

    /**
     * @var $memberDAO \Powon\Dao\MemberDAO
     */
    $memberDAO = $this->daoFactory->getMemberDAO();

    /**
     * @var $sessionService \Powon\Services\SessionService
     */
    $sessionService = $this->sessionService;

    $user = $sessionService->getAuthenticatedMember();
    if (!$sessionService->isAdmin()) {
        $logger->alert('Non admin member is requesting list of users', $user ? $user->toObject() : ['Anonymous user']);
        return $response->withStatus(403, 'Forbidden');
    }
    $logger->info("Admin member is requesting list of members", $user->toObject());
    $members = $memberDAO->getAllMembers();
    $memberObjects = array_map(function(\Powon\Entity\Member $each) {
        return $each->toObject();
    }, $members);
    $response->getBody()->write(json_encode($memberObjects));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

/**
 * Validates that all required fields are there
 * @param $p array of parameters
 * @return true|false
 */
function registration_validate_parameters($p) {
    $valid = function ($name) use ($p) {
        return (isset($p[$name]) && !empty($p[$name]));
    };
    return (
        $valid('username') && $valid('first_name') && $valid('last_name')
        && $valid('date_of_birth') && $valid('user_email')
    );
}

$app->post('/api/v1/register', function(Request $request, Response $response) {
    $params = $request->getParsedBody();
    if (registration_validate_parameters($params)) {
        /**
         * @var $regS \Powon\Services\MemberService
         */
        $regS = $this->memberService;
        $result = $regS->registerNewMember($params['username'],
            $params['user_email'],
            $params['password'],
            $params['date_of_birth'],
            $params['first_name'],
            $params['last_name']);
        if ($result['success']) {
            $responseObj = [
                'status' => 'success',
                'message' => $result['message']
            ];
            $response->getBody()->write(json_encode($responseObj));
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } else {
            $responseObj = [
                'status' => 'failure',
                'message' => $result['message']
            ];
            $response->getBody()->write(json_encode($responseObj));
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    } else {
        $responseObj = [
            'status' => 'failure',
            'message' => 'Invalid parameters given. Valid ones are: '.
            'user_email, password, date_of_birth, first_name, last_name'
        ];
        $response->getBody()->write(json_encode($responseObj));
        return $response->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }
});