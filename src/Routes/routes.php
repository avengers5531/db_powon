<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

// routes go here
// TODO organize routes?

$app->get('/', function (Request $request, Response $response){
  //TODO: Add posts to home page.
    /**
     * @var \Powon\Services\PostService $postService
     */
    $postService = $this->postService;

    $current_member = $this->sessionService->getAuthenticatedMember();
    $posts_can_edit = [];
    $posts_comment_count = [];
    $additional_info_tab = [];
    if (!$current_member) {
        $posts = $postService->getPublicPosts();
    } else {
        $posts = $postService->getHomePagePosts($current_member);
        foreach ($posts as &$post) {
            $page_id = $post->getPageId();
            $memberPage = $this->memberPageService->getMemberPageByPageId($page_id);
            $additional_info = null;
            if ($memberPage) {
                $additional_info['memberPage'] = $memberPage;
                $additional_info['member'] = $this->memberService->getMemberById($memberPage->getMemberId());
            } else { // must be a group
                $groupPage = $this->groupPageService->getPageById($page_id);
                $group = $this->groupService->getGroupById($groupPage->getPageGroupId());
                $additional_info['group'] = $group;
                $additional_info['groupPage'] = $groupPage;
            }
            $posts_can_edit[$post->getPostId()] = $postService->canMemberEditPost($current_member, $post, $additional_info);
            $posts_comment_count[$post->getPostId()] = count($postService->getPostCommentsAccessibleToMember($current_member, $post, $additional_info));
            $additional_info_tab[$post->getPostId()] = $additional_info;
        }
    }

  $response = $this->view->render($response, "main-page.html", [
      'is_authenticated' => $this->sessionService->isAuthenticated(),
      'menu' => [
        'active' => 'home'
      ],
      'current_member' => $current_member,
      'posts' => $posts,
      'posts_can_edit' => $posts_can_edit,
      'posts_comment_count' => $posts_comment_count,
      'additional_info_tab' => $additional_info_tab
  ]);
  return $response;
})->setName('root');


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

// *** ADMIN ROUTES **** //

//View members to edit (admin only)
$app->get('/view-members', function (Request $request, Response $response) {
    $auth_status = $this->sessionService->isAuthenticated();
    $logger = $this->logger;
    $memberService = $this->memberService;
    $sessionService = $this->sessionService;
    $current_member = $sessionService->getAuthenticatedMember();
    $logger->info("Member twig list");
    $members = $memberService->getAllMembers();
    //$members = array_map(function ($it) use ($memberService) {
      //  $memberService->populateInterestsForMember($it);
       // return $it;
    //}, $members);
    //$logger->debug('Member 3:', $members[3]->toObject());
    $response = $this->view->render($response, "view-members.html", ["members" => $members,
        'current_member' => $current_member, 'is_authenticated' => $auth_status]);
    return $response;
});

//Admin view of update profile (can edit any profile)
$app->get('/view-members/{username}', function(Request $request, Response $response){
    $logger=$this->logger;
    $username = $request->getAttribute('username');
    $member = $this->memberService->getMemberByUsername($username); //gets the member object in the URL
    $is_admin = $this->sessionService->isAdmin();
    //$status = $this->memberService->getStatus();
    if ($is_admin){
        $this->memberService->populateInterestsForMember($member);
        $member = $this->memberService->populateProfessionForMember($member);
        $member = $this->memberService->populateRegionForMember($member);
        $logger->info("Get status of member before rendering: " . $member->getStatus());
        //TODO: what variables does edit-member need passed from the route to render properly
        $response = $this->view->render($response, "edit-member.html", [
            'is_admin' => $is_admin,
            //'status' => $status,
            'menu' => [
                'active' => 'profile'
            ],
            'current_member' => $this->sessionService->getAuthenticatedMember(),
            'member' => $member,
            'interests' => $this->memberService->getAllInterests(),
            'professions' => $this->memberService->getAllProfessions(),
        ]);
        return $response;
    }
    return $response->withRedirect('/'); // Permission denied
})->setname('edit-member');

//update details (authenticate admin)
$app->post('/view-member/{username}/update_details', function(Request $request, Response $response){
    $logger = $this->logger;
    $username = $request->getAttribute('username');
    $member = $this->memberService->getMemberByUsername($username);
    $is_admin = $this->sessionService->isAdmin();
    if ($is_admin){
        $params = $request->getParsedBody();
        $res = $this->memberService->updatePowonMemberAdmin($member, $params);
        $logger->info("updated using updatePowonMemberAdmin");
        return $response->withRedirect("/view-members/$username");
    }
    return $response->withRedirect('/'); // Permission denied
})->setname('edit-member-update');


// POST route for delete member, calls the service to delete the member.
// TODO check access.
$app->post('/delete/{member_id}', function (Request $request, Response $response){
    /**
     * @var $memberService \Powon\Services\MemberService
     */

    $memberService = $this->memberService;

    /**
     * @var $sessionService \Powon\Services\SessionService
     */
    $sessionService = $this->sessionService;
    $member_id = $request->getAttribute('member_id');
    $params = $request->getParsedBody();
    $this->logger->debug("Got a request to delete member $member_id", $params);
    $res = $memberService->deleteMember($member_id);
    if ($res) {
        $sessionService->getSession()->addSessionData('flash',['post_success_message' => "Member $member_id deleted successfully!"]);
        return $response->withRedirect('/view-members');
    } else {
        $sessionService->getSession()->addSessionData('flash',['post_error_message' => "Could not delete member $member_id!"]);
        return $response->withRedirect('/view-members');
    }
})->setName('member-delete');

//View unpaid invoices (admin only)
$app->get('/admin-invoice', function (Request $request, Response $response) {
    /**
     * @var \Powon\Services\InvoiceService $invoiceService
     */
    //$invoiceService = $this->invoiceService;
    $auth_status = $this->sessionService->isAuthenticated();
    $logger = $this->logger;
    //$logger->info("invoice service accessed");
    $current_member = $this->sessionService->getAuthenticatedMember();
    $logger->info("Admin access invoices");
    $unpaid_invoices = $this->invoiceService->getUnpaidInvoices();
    $response = $this->view->render($response, "admin-invoice.html", ["unpaid_invoices" => $unpaid_invoices,
        'current_member' => $current_member, 'is_authenticated' => $auth_status]);
    return $response;
})->setName('admin-invoice');


// *** END ADMIN ROUTES **** //


// Login route
$app->post('/login', function (Request $request, Response $response) {
    $params = $request->getParsedBody();
    $rememberme = false;
    if (isset($params['remember']) && $params['remember'] === 'on')
        $rememberme = true;

    if (!(isset($params['username']) && isset($params['password']))
    ) {
        $errorMessage = 'Fields must not be empty';
    } else {
        $res = $this->sessionService->authenticateUserByUsername($params['username'], $params['password'], $rememberme);
        if ($res['success']) {
            return $response->withRedirect('/'); // we logged in!
        } else {
            $errorMessage = $res['message'];
        }
    }
    // rerender the view with the login error message
    $response = $this->view->render($response, 'main-page.html', [
        'is_authenticated' => false,
        'login_error_message' => $errorMessage,
        'username' => isset($params['username']) ? $params['username'] : '',
        'menu' => [
            'active' => 'home'
        ],
        'posts' => $this->postService->getPublicPosts(),
    ]);
    return $response;

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
    return $response;
});

require 'member_routes.php';
require 'group_routes.php';
require 'post_routes.php';

require 'Api/registration.php';
require 'Api/members.php';

//TODO test route to remove later
$app->get('/template/{template_name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('template_name');
    return $this->view->render($response, $name, ['post' => $this->postService->getPostById(1)]);
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
