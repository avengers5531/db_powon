<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

// routes go here
// TODO organize routes?

$app->get('/', function (Request $request, Response $response){
  //TODO: Add posts to home page.
  $response = $this->view->render($response, "main-page.html", [
      'is_authenticated' => $this->sessionService->isAuthenticated(),
      'menu' => [
        'active' => 'home'
      ],
      'current_member' => $this->sessionService->getAuthenticatedMember()
  ]);
  return $response;
});

//TODO test route to remove later
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    //return $this->renderer->render($response, 'index.phtml', ['name' => $name]);
    return $response;
});

//TODO test route to remove later
$app->get('/membersNonTwig', function (Request $request, Response $response) {
    $this->logger->addInfo("Member list");
    $members = $this->memberService->getAllMembers();
    $response = $this->renderer->render($response, "members.phtml", ["members" => $members, "router" => $this->router]);
    return $response;
});

//TODO test route to remove later
$app->get('/members', function (Request $request, Response $response) {
    /**
     * @var $logger \Psr\Log\LoggerInterface
     */
    $logger = $this->logger;

    /**
     * @var $memberDAO \Powon\Dao\MemberDAO
     */
    $memberDAO = $this->daoFactory->getMemberDAO();

    $logger->info("Member twig list");
    $members = $memberDAO->getAllMembers();
    $response = $this->view->render($response, "members.twig", ["members" => $members]);
    return $response;
});

// Login route 
$app->post('/login', function (Request $request, Response $response) {
    $params = $request->getParsedBody();
    if (!(isset($params['username']) &&
          isset($params['password']) &&
          $this->sessionService->authenticateUserByUsername($params['username'], $params['password']))
    ) {
        // rerender the view with the login error message
        $errorMessage = 'Invalid username and password combination.';
        $response = $this->view->render($response, 'main-page.html', [
            'is_authenticated' => false,
            'login_error_message' => $errorMessage,
            'username' => isset($params['username']) ? $params['username'] : '',
            'menu' => [
                'active' => 'home'
            ]
        ]);
        return $response;
    } else {
        return $response->withRedirect('/');
    }
});

// Logout route
$app->get('/logout', function(Request $request, Response $response) {
    if ($this->sessionService->isAuthenticated()) {
        // Trust the session service to destroy the current session
        if (!$this->sessionService->destroySession()) {
            $this->logger->warning("Session wasn't destroyed properly...");
        }
    }
    return $response->withRedirect('/');
});

// New member creation (receive request from UI)
// TODO return a response with a UI.
$app->post('/register', function(Request $request, Response $response) {
    $code = 200;
    if (!$this->sessionService->isAuthenticated()) {
        $params = $request->getParsedBody();
        $res = $this->memberService->registerPowonMember($params);
        if (!$res['success']) {
            $code = 400;
        }
    } else { // is authenticated
        $msg = 'Authenticated user sent a request to register!';
        $code = 400;
        $this->logger->warning($msg);
        $res = ['success' => false, 'message' => $msg];
    }
    return $response->withJson($res, $code);
});

require 'Api/registration.php';
