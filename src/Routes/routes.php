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
})->setname('root');


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
     * @var $memberService \Powon\Services\MemberService
     */
    $memberService = $this->memberService;

    $logger->info("Member twig list");
    $members = $memberService->getAllMembers();
    $members = array_map(function ($it) use ($memberService) {
        $memberService->populateInterestsForMember($it);
        return $it;
    }, $members);
    //$logger->debug('Member 3:', $members[3]->toObject());
    $response = $this->view->render($response, "members.twig", ["members" => $members]);
    return $response;
});

// Login route
$app->post('/login', function (Request $request, Response $response) {
    $params = $request->getParsedBody();
    $rememberme = false;
    if (isset($params['remember']) && $params['remember'] === 'on')
        $rememberme = true;
    if (!(isset($params['username']) &&
          isset($params['password']) &&
          $this->sessionService->authenticateUserByUsername($params['username'], $params['password'], $rememberme))
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
        $token = $this->sessionService->getSession()->getToken();
        if (!$this->sessionService->destroySession()) {
            $this->logger->warning("Session wasn't destroyed properly...", ['token' => $token]);
        }
    }
    return $response->withRedirect('/');
});

// New member creation (receive request from UI)
$app->post('/register', function(Request $request, Response $response) {
    if (!$this->sessionService->isAuthenticated()) {
        $params = $request->getParsedBody();
        $res = $this->memberService->registerPowonMember($params);
        $response = $this->view->render($response, "register.html", [
            'prev_val' => $params,
            'registration_success' => $res['success'],
            'registration_message' => $res['message']
        ]);
        return $response;
    } else { // is authenticated
        $this->logger->warning('Authenticated user sent a request to register!');
        return $response->withRedirect('/');
    }
});

// Displays the registration form
$app->get('/register', function(Request $request, Response $response) {
    if ( !$this->sessionService->isAuthenticated() ) {
        $response = $this->view->render($response, "register.html");
    } else {
        return $response->withRedirect('/');
    }
});

require 'member_routes.php';
require 'group_routes.php';

require 'Api/registration.php';
require 'Api/members.php';

//TODO test route to remove later
$app->get('/template/{template_name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('template_name');
    return $this->view->render($response, $name);
});


$app->group('/search/members', function(){
    $this->get('', function (Request $request, Response $response) {
        if ($this->sessionService->isAuthenticated()) {
            $response = $this->view->render($response, "search-members-page.html", [
                    'is_search' => false,
                    'menu' => [
                      'active' => 'members'
                    ]
                ]);
        } else { // not authenticated
            $this->logger->warning('Unauthenticated user requested the search page.');
            return $response->withRedirect('/');
        }
    })->setName('search-members');
    $this->post('', function (Request $request, Response $response) {
        if ($this->sessionService->isAuthenticated()) {
            $params = $request->getParsedBody();
            $auth_member = $this->sessionService->getAuthenticatedMember();

            $res = $this->memberService->searchMembers($auth_member,$params);

            $response = $this->view->render($response, "search-members-page.html", [
                    'is_search' => true,
                    'menu' => [
                      'active' => 'members'
                    ],
                    'members' => $res
                ]);
        } else { // not authenticated
            $this->logger->warning('Unauthenticated user requested the search page.');
            return $response->withRedirect('/');
        }
    });
});
