<?php

use Powon\Entity\Group;
use Powon\Entity\Member;
use Powon\Services\GroupPageService;
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

    /**
     * @var \Powon\Services\GroupPageService $groupPageService
     */
    $groupPageService = $container->groupPageService;

    // Routes for creating a group.
    
    // GET route for /group/create (returns the create group form)
    $this->get('/create', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $sessionData = $sessionService->getSession()->getSessionData();
        $post_error_message = null;
        if (isset($sessionData['flash'])) {
            if (isset($sessionData['flash']['post_error_message'])) {
                $post_error_message = $sessionData['flash']['post_error_message'];
            }
            // flash data is consumed
            $sessionService->getSession()->removeSessionData('flash');
        }
        $response = $this->view->render($response, "create-group.html", [
            'is_authenticated' => $sessionService->isAuthenticated(),
            'current_member' => $sessionService->getAuthenticatedMember(),
            'menu' => ['active' => 'groups'],
            'post_error_message' => $post_error_message
        ]);
        return $response;
    })->setName('group-create');

    // POST route for /group/create, calls the service to create a group.
    $this->post('/create', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $params = $request->getParsedBody();
        $current_member = $sessionService->getAuthenticatedMember();
        $this->logger->debug('Got a request to create groups.', $params);
        $res = $groupService->createGroupOwnedBy($current_member->getMemberId(), $params);
        if ($res['success']) {
            $sessionService->getSession()->addSessionData('flash',['post_success_message' => $res['message']]);
            return $response->withRedirect($this->router->pathFor('view-groups'));
        } else {
            $sessionService->getSession()->addSessionData('flash',['post_error_message' => $res['message']]);
            return $response->withRedirect($this->router->pathFor('group-create'));
        }
    });

    // POST route for /group/update, calls the service to update a group.
    // TODO check access.
    $this->post('/update/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $this->logger->debug("Got a request to update group $group_id", $params);
        $res = $groupService->updateGroup($group_id, $params);
        if ($res) {
            $sessionService->getSession()->addSessionData('flash',['post_success_message' => "Group $group_id updated successfully!"]);
        } else {
            $sessionService->getSession()->addSessionData('flash',['post_error_message' => "Could not update group $group_id!"]);
        }
        return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
    })->setName('group-update');

    // POST route for /group/delete, calls the service to delete the group.
    // TODO check access.
    $this->post('/delete/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $this->logger->debug("Got a request to delete group $group_id", $params);
        $res = $groupService->deleteGroup($group_id);
        if ($res) {
            $sessionService->getSession()->addSessionData('flash',['post_success_message' => "Group $group_id deleted successfully!"]);
            return $response->withRedirect($this->router->pathFor('view-groups'));
        } else {
            $sessionService->getSession()->addSessionData('flash',['post_error_message' => "Could not delete group $group_id!"]);
            return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
        }
    })->setName('group-delete');

    $this->post('/join/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $current_member = $sessionService->getAuthenticatedMember();
        $this->logger->debug("Got a request to join group $group_id", $params);
        $res = $groupService->createRequestToJoinGroup($current_member->getMemberId(), $group_id);
        if ($res) {
            $sessionService->getSession()->addSessionData('flash',['post_success_message' => "The request was sent."]);
        } else {
            $sessionService->getSession()->addSessionData('flash',['post_error_message' => "Could not send a request. Have you already sent it before?"]);
        }
        return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
    })->setName('group-join');

    // Leave group:
    $this->post('/leave/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $current_member = $sessionService->getAuthenticatedMember();
        $this->logger->debug("Got a request to leave group $group_id", $params);
        $res = $groupService->removeMemberFromGroup($current_member->getMemberId(), $group_id);
        if ($res) {
            $sessionService->getSession()->addSessionData('flash',['post_success_message' => "You have left the group."]);
        } else {
            $sessionService->getSession()->addSessionData('flash',['post_error_message' => "Could not send a request. Have you already sent it before?"]);
        }
        return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
    })->setName('group-leave');

    $this->get('/view', function (Request $request, Response $response)
    use ($groupService, $sessionService)
    {
        $current_member = $sessionService->getAuthenticatedMember();
        $my_groups = $groupService->getGroupsMemberBelongsTo($current_member->getMemberId());
        $sessionData = $sessionService->getSession()->getSessionData();
        $post_error_message = null;
        $post_success_message = null;
        if (isset($sessionData['flash'])) {
            if (isset($sessionData['flash']['post_error_message'])) {
                $post_error_message = $sessionData['flash']['post_error_message'];
            }
            if (isset($sessionData['flash']['post_success_message'])) {
                $post_success_message = $sessionData['flash']['post_success_message'];
            }
            // flash data is consumed
            $sessionService->getSession()->removeSessionData('flash');
        }
        $response = $this->view->render($response, 'view-groups.html', [
            'groups' => $my_groups,
            'menu' => ['active' => 'groups'],
            'current_member' => $current_member,
            'post_error_message' => $post_error_message,
            'post_success_message' => $post_success_message
        ]);
        return $response;
    })->setName('view-groups');

    // Group view (lists the group pages)
    $this->get('/view/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService, $groupPageService)
    {
        $group_id = $request->getAttribute('group_id');
        $group = $groupService->getGroupById($group_id);
        if (!$group) {
            // TODO redirect to not found
            return $response->withStatus(404);
        }
        $this->logger->debug('Group fetched is', $group->toObject());
        $current_member = $sessionService->getAuthenticatedMember();
        $member_waiting_for_approval = false;
        $member_belongs_to_group = $groupService->memberBelongsToGroup($current_member->getMemberId(), $group_id);
        if (!$member_belongs_to_group)
            $member_waiting_for_approval = $groupService->memberWaitingForApproval($current_member->getMemberId(), $group_id);
        $sessionData = $sessionService->getSession()->getSessionData();
        $post_error_message = null;
        $post_success_message = null;
        if (isset($sessionData['flash'])) {
            if (isset($sessionData['flash']['post_error_message'])) {
                $post_error_message = $sessionData['flash']['post_error_message'];
            }
            if (isset($sessionData['flash']['post_success_message'])) {
                $post_success_message = $sessionData['flash']['post_success_message'];
            }
            // flash data is consumed
            $sessionService->getSession()->removeSessionData('flash');
        }
        $response = $this->view->render($response, 'view-group.html', [
            'current_group' => $group,
            'menu' => ['active' => 'groups'],
            'current_member' => $current_member,
            'member_belongs_to_group' => $member_belongs_to_group,
            'member_waiting_for_approval' => $member_waiting_for_approval,
            'post_error_message' => $post_error_message,
            'post_success_message' => $post_success_message,
            'pages' => $groupPageService->getGroupPagesForMember($group_id, $current_member->getMemberId())
        ]);
        return $response;
    })->setName('view-group');

    $this->get('/search', function (Request $request, Response $response)
    use ($groupService, $sessionService) {
        $params = $request->getQueryParams();
        $groups = [];
        $this->logger->debug("Received search request: ", $params ?: []);
        if ($params && isset($params['search_term'])) {
            $search_term = $params['search_term'];
            $groups = $groupService->searchGroups($search_term);
        }
        $current_member = $sessionService->getAuthenticatedMember();
        $response = $this->view->render($response, 'search-groups.html', [
            'groups' => $groups,
            'menu' => ['active' => 'groups'],
            'current_member' => $current_member,
        ]);
        return $response;

    })->setName('search-groups');

    // Manage group members /group/manage/{group_id}
    $this->get('/manage/{group_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService)
    {
        $group_id = $request->getAttribute('group_id');
        $group = $groupService->getGroupById($group_id);
        if (!$group) {
            //TODO redirect to NOT FOUND view
            return $response->withStatus(404);
        }
        $this->logger->debug('Group is', $group->toObject());
        $pending_members = $groupService->getMembersWithPendingRequestsToGroup($group_id);
        $group_members = $groupService->getGroupMembers($group_id);
        $sessionData = $sessionService->getSession()->getSessionData();
        $post_error_message = null;
        $post_success_message = null;
        if (isset($sessionData['flash'])) {
            if (isset($sessionData['flash']['post_error_message'])) {
                $post_error_message = $sessionData['flash']['post_error_message'];
            }
            if (isset($sessionData['flash']['post_success_message'])) {
                $post_success_message = $sessionData['flash']['post_success_message'];
            }
            // flash data is consumed
            $sessionService->getSession()->removeSessionData('flash');
        }

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
    })->setName('group-manage');

    /**
     * Accept users or remove them from the group.
     * @param Request $request The request object.
     * @param Response $response The response object.
     * @param callable $action Takes a member_id and group_id and returns a boolean.
     * @param string $initialErrorMessage A descriptive error message. It will be followed by a list of ids in case of error.
     * @param string $successMessage A descriptive message when the operation is successful
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

        return $response->withRedirect($container->router->pathFor('group-manage', ['group_id' => $group_id]));
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
    })->setName('group-manage-accept');

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
    })->setName('group-manage-remove');

    $this->post('/{group_id}/page/create', function(Request $request, Response $response)
    use ($groupService, $sessionService, $groupPageService) {
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $this->logger->debug('Params to create a group page: ', $params);
        $owner_id = $sessionService->getAuthenticatedMember()->getMemberId();
        $res = $groupPageService->createGroupPage($owner_id, $group_id, $params);
        if ($res['success']) {
            $sessionService->getSession()->addSessionData('flash', ['post_success_message' => $res['message']]);
            return $response->withRedirect($this->router->pathFor('view-group-page', ['page_id' => $res['page_id']]));
        } else {
            $sessionService->getSession()->addSessionData('flash', ['post_error_message' => $res['message']]);
            return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
        }
    })->setName('page-create');

    $this->post('/page/{page_id}/update', function(Request $request, Response $response)
    use ($groupService, $sessionService, $groupPageService) {
        $page_id = $request->getAttribute('page_id');
        $params = $request->getParsedBody();
        $this->logger->debug('Params to update a group page: ', $params);
        $current_member = $sessionService->getAuthenticatedMember();
        // TODO get access
        $res = $groupPageService->updateGroupPage($page_id, $params);
        if ($res['success']) {
            $sessionService->getSession()->addSessionData('flash', ['post_success_message' => $res['message']]);
        } else {
            $sessionService->getSession()->addSessionData('flash', ['post_error_message' => $res['message']]);
        }
        return $response->withRedirect($this->router->pathFor('view-group-page', ['page_id' => $page_id]));
    })->setName('page-update');

    $this->post('/page/{page_id}/update-access', function(Request $request, Response $response)
    use ($groupService, $sessionService, $groupPageService) {
        $page_id = $request->getAttribute('page_id');
        $params = $request->getParsedBody();
        $page = $groupPageService->getPageById($page_id);
        $group = $groupService->getGroupById($page->getPageGroupId());
        if (!isset($params[GroupPageService::FIELD_ACCESS])) {
            return $response->withStatus(400); // bad request
        }
        $access_type = $params[GroupPageService::FIELD_ACCESS];
        $this->logger->debug('Params to update group page access: ', $params);
        $current_member = $sessionService->getAuthenticatedMember();
        if (!($current_member->isAdmin() ||
            $current_member->getMemberId() == $group->getGroupOwner() ||
            $current_member->getMemberId() == $page->getPageOwner()))
        {
            return $response->withStatus(403); // forbidden
        }
        // construct list of members from the request parameters
        $members = array();
        foreach ($params as $key => $val) {
            //ignore val
            if ($key !== GroupPageService::FIELD_ACCESS && is_numeric($key)) {
                $members[] = $key;
            }
        }
        $page_owner = $page->getPageOwner();
        $res = $groupPageService->updatePageAccess($page_id, $page->getPageGroupId(), $access_type, $members, $page_owner);
        if ($res['success']) {
            $sessionService->getSession()->addSessionData('flash', ['post_success_message' => $res['message']]);
            return $response->withRedirect($this->router->pathFor('view-group-page', ['page_id' => $page_id]));
        } else {
            $sessionService->getSession()->addSessionData('flash', ['post_error_message' => $res['message']]);
            return $response->withRedirect($this->router->pathFor('page-manage-access', ['page_id' => $page_id]));
        }
    })->setName('page-update-access');

    $this->get('page/{page_id}/manage-access', function (Request $request, Response $response)
    use ($groupService, $sessionService, $groupPageService) {
        $page_id = $request->getAttribute('page_id');
        $page = $groupPageService->getPageById($page_id);
        $group = $groupService->getGroupById($page->getPageGroupId());
        $current_member = $sessionService->getAuthenticatedMember();

        if (!($current_member->isAdmin() ||
            $current_member->getMemberId() == $group->getGroupOwner() ||
            $current_member->getMemberId() == $page->getPageOwner()))
        {
            return $response->withStatus(403); // forbidden
        }
        $members_with_access = array();
        $members = $groupPageService->getMembersWithAccessToPage($page_id, $group->getGroupId());
        foreach ($members as &$member) {
            $members_with_access[$member->getMemberId()] = true;
        }
        $this->logger->debug("Members with access to page $page_id.", $members_with_access);

        // do not show the group owner in the list to give access to
        $display_members = array_filter($groupService->getGroupMembers($group->getGroupId()), function (Member $member)
        use ($page)
        {
           return $member->getMemberId() != $page->getPageOwner();
        });
        return $this->view->render($response, 'manage-page-access.html', [
            'title' => 'Manage page access',
            'current_group' => $group,
            'current_member' => $current_member,
            'page' => $page,
            'group_members' => $display_members,
            'menu' => ['active' => 'groups'],
            'members_with_access' => $members_with_access
        ]);

    })->setName('page-manage-access');

    $this->get('/page/{page_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService, $groupPageService) {
        $page_id = $request->getAttribute('page_id');
        $page = $groupPageService->getPageById($page_id);
        if (!$page) {
            return $response->withStatus(404);
        }
        $group = $groupService->getGroupById($page->getPageGroupId());
        $current_member = $sessionService->getAuthenticatedMember();
        $can_administer = $current_member->isAdmin() ||
            $current_member->getMemberId() == $group->getGroupOwner() ||
            $current_member->getMemberId() == $page->getPageOwner();

        $post_error_message = null;
        $post_success_message = null;
        $sessData = $sessionService->getSession()->getSessionData();
        if (isset($sessData['flash'])) {
            if (isset($sessData['flash']['post_error_message'])) {
                $post_error_message = $sessData['flash']['post_error_message'];
            }
            if (isset($sessData['flash']['post_success_message'])) {
                $post_success_message = $sessData['flash']['post_success_message'];
            }
            $sessionService->getSession()->removeSessionData('flash');
        }
        $response = $this->view->render($response, 'view-group-page.html', [
            'title' => $page->getPageTitle(),
            'current_member' => $current_member,
            'current_group' => $group,
            'current_page' => $page,
            'can_administer' => $can_administer,
            'menu' => ['active' => 'groups'],
            'post_success_message' => $post_success_message,
            'post_error_message' => $post_error_message
        ]);
        return $response;

    })->setName('view-group-page');

    //Delete group page
    $this->post('/deletePage/{page_id}', function(Request $request,Response $response)
    use($groupPageService, $sessionService){
        $page_id = $request->getAttribute('page_id');
        $page = $groupPageService->getPageById($page_id);
        $page_title = $page->getPageTitle();
        $group_id = $page->getPageGroupId();
        $params = $request->getParsedBody();
        $this->logger->debug("Received request to delete group page $page_id", $params);
        $res = $groupPageService->deleteGroupPage($page_id);
        if($res){
            $sessionService->getSession()->addSessionData('flash', ['post_success_message' => "Group page ' $page_title ' deleted successfully."]);
            return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
        }
        else{
            $sessionService->getSession()->addSessionData('flash', ['post_error_message' => "Could not delete group page $page_id"]);
            return $response->withRedirect($this->router->pathFor('view-group-page', ['page_id' => $page_id]));
        }
    })->setName('group-page-delete');

    //Add new member to group
    $this->post('/manage/{group_id}', function (Request $request, Response $response)
    use($groupService, $sessionService, $performActionOnUser){
        $group_id = $request->getAttribute('group_id');
        $params = $request->getParsedBody();
        $member_id = $params['id'];
        $this->logger->debug("Got a request to add new member to group $group_id", $params);
        $res1 = $groupService->createRequestToJoinGroup($member_id, $group_id);
        if($res1){
            $res2 = $groupService->acceptRequestToJoinGroup($member_id, $group_id);

            if ($res2) {
                $sessionService->getSession()->addSessionData('flash',['post_success_message' => "New member was added to group."]);
            } else {
                $sessionService->getSession()->addSessionData('flash',['post_error_message' => "Could not add member to group"]);
            }

            return $response->withRedirect($this->router->pathFor('view-group', ['group_id' => $group_id]));
        }
    })->setName('group-add-member');

});
// TODO add middleware to check permission and directly return a forbidden if user is not authenticated.
