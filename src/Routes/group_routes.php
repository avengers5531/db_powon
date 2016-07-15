<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

// http://www.slimframework.com/docs/objects/router.html#route-groups
$app->group('/group', function () use ($container) {
    
    /**
     * @var \Powon\Services\GroupService $groupService
     */
    $groupService =  $container->groupService;

    /**
     * @var \Powon\Services\SessionService $sessionService
     */
    $sessionService = $container->sessionService;

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
    })->setName('group_create');

    // POST route for /group/create, calls the service to create a group.
    $this->post('/create', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $params = $request->getParsedBody();
        $current_member = $sessionService->getAuthenticatedMember();
        $this->logger->debug('Got a request to create groups.', $params);
        $res = $groupService->createGroupOwnedBy($current_member->getMemberId(), $params);
        if ($res) {
            $code = $res['success'] ? 200 : 400;
            // TODO redirect to view group page
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

    $this->get('/manage/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService)
    {
        $group_id = $request->getAttribute('group_id');
        $group = $groupService->getGroupById($group_id);
        $this->logger->debug('Group is', $group->toObject());
        $pending_members = $groupService->getMembersWithPendingRequestsToGroup($group_id);
        $group_members = $groupService->getGroupMembers($group_id);
        $sessionData = $sessionService->getSession()->getSessionData();
        $post_error_message = null;
        $post_success_message = null;
        if (isset($sessionData['flash'])) {
            if (isset($sessionData['flash']['post_error_message'])) {
                $post_error_message = $sessionData['flash']['post_error_message'];
                unset($sessionData['flash']['post_error_message']);
            }
            if (isset($sessionData['flash']['post_success_message'])) {
                $post_success_message = $sessionData['flash']['post_success_message'];
                unset($sessionData['flash']['post_success_message']);
            }
            unset($sessionData['flash']);
        }
        // flash data is consumed
        $sessionService->getSession()->setSessionData($sessionData);
        $response = $this->view->render($response, 'manage-group-users.html', [
            'pending_members' => $pending_members,
            'group_members' => $group_members,
            'group' => $group,
            'menu' => ['active' => 'groups'],
            'current_member' => $sessionService->getAuthenticatedMember(),
            'post_success_message' => $post_success_message,
            'post_error_message' => $post_error_message
        ]);
        return $response;
    })->setName('group_manage');

    /**
     * Accept users or remove them from the group.
     * @param Request $request The request object.
     * @param Response $response The response object.
     * @param callable $action Takes a member_id and group_id and returns a boolean.
     * @param $initialErrorMessage A descriptive error message. It will be followed by a list of ids in case of error.
     * @param $successMessage A descriptive message when the operation is successful
     * @return Response a response containing a redirect to the manage groups page.
     */
    $performActionOnUser = function (Request $request,
                                     Response $response,
                                     callable $action,
                                     $initialErrorMessage,
                                     $successMessage)
    use ($sessionService, $container)
    {
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $post_error_message = null;
        $post_success_message = null;
        foreach ($params as $key => $val) {
            if (!$action($key, $group_id)) {
                if (!$post_error_message)
                    $post_error_message = $initialErrorMessage ." $key";
                else
                    $post_error_message = $post_error_message . ", $key";
            }
        }
        if (!$post_error_message)
            $post_success_message = $successMessage;

        $flash = ['post_error_message' => $post_error_message, 'post_success_message' => $post_success_message];
        $sessionService->getSession()->addSessionData('flash',$flash);

        return $response->withRedirect($container->router->pathFor('group_manage', ['group_id' => $group_id]));
    };

    $this->post('/manage/accept_users/{group_id}', function (Request $request, Response $response)
    use ($performActionOnUser, $groupService)
    {
        $response = $performActionOnUser($request, $response,
            function ($member_id, $group_id)
            use ($groupService) {
                return $groupService->acceptRequestToJoinGroup($member_id, $group_id);
            },
            'Could not accept user(s) with id',
            'Selected members were accepted into this group.');
        return $response;
    })->setName('group_manage_accept');

    $this->post('/manage/remove_users/{group_id}', function (Request $request, Response $response)
    use ($groupService, $performActionOnUser)
    {
        $response = $performActionOnUser($request, $response,
            function ($member_id, $group_id)
            use ($groupService) {
                return $groupService->removeMemberFromGroup($member_id, $group_id);
            },
            'Could not delete user(s) with id ',
            'Selected members were removed from this group.');
        return $response;
    })->setName('group_manage_remove');

});
// TODO add middleware to check permission and directly return a forbidden if user is not authenticated.
