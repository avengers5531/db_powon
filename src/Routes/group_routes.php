<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

// http://www.slimframework.com/docs/objects/router.html#route-groups
$app->group('/group', function () use ($container) {
    
    $c = $this->getContainer();
    
    /**
     * @var \Powon\Services\GroupService $groupService
     */
    $groupService =  $c->groupService;

    /**
     * @var \Powon\Services\SessionService $sessionService
     */
    $sessionService = $c->sessionService;

    // Routes for creating a group.
    
    // GET route for group create (returns the create group form)
    $this->get('/create', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $response = $this->view->render($response, "create-group.html", [
            'is_authenticated' => $sessionService->isAuthenticated(),
            'current_member' => $sessionService->getAuthenticatedMember(),
            'menu' => ['active' => 'groups']
        ]);
        return $response;
    });

    // POST route for /group/create, calls the service to create a group.
    $this->post('/create', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $params = $request->getParsedBody();
        $current_member = $sessionService->getAuthenticatedMember();
        $this->logger->debug('Got a request to create groups.', $params);
        $res = $groupService->createGroupOwnedBy($current_member->getMemberId(), $params);
        if ($res) {
            $code = $res['success'] ? 200 : 400;
            // TODO return a view
            return $response->withJson($res, $code);
        } else {
            $response = $this->view->render($response, "create-group.html", [
                'is_authenticated' => $sessionService->isAuthenticated(),
                'current_member' => $sessionService->getAuthenticatedMember(),
                'post_error_message' => 'This feature is coming soon!',
                'menu' => ['active' => 'groups']
            ]);
            return $response;
        }
    });

});
// TODO add middleware to check permission and directly return a forbidden if user is not authenticated.
