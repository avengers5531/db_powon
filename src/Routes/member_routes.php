<?php
use Powon\Entity\Post;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

$app->group('/members/{username}', function(){

    /**
     * A member's profile page
     */
    $this->get('', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        if ($auth_status){
            $username = $request->getAttribute('username');
            $this->logger->addInfo("Member page for $username");
            $member = $this->memberService->getMemberByUsername($username);
            $auth_member = $this->sessionService->getAuthenticatedMember();
            $relationship = null;
            if ($member->getMemberId() === $auth_member->getMemberId()){
                $on_own_profile = true;
            }
            else{
                $on_own_profile = false;
                $relationship = $this->relationshipService->checkRelationship($member, $auth_member);
            }
            $this->memberService->populateInterestsForMember($member);
            $member = $this->memberService->populateProfessionForMember($member);
            $member = $this->memberService->populateRegionForMember($member);
            $page = $this->memberPageService->getMemberPageByMemberId($member->getMemberId());
            $additionalInfo = ['memberPage' => $page, 'member' => $member];
            $posts = $this->postService->getPostsForMemberOnPage($auth_member,
                $page->getPageId(), $additionalInfo);

            $posts_can_edit = [];
            $posts_comment_count = [];
            foreach ($posts as &$post) {
                $id = $post->getPostId();
                $posts_can_edit[$id]
                    = $this->postService->canMemberEditPost($auth_member, $post, $additionalInfo);
                $posts_comment_count[$id] = count($this->postService->getPostCommentsAccessibleToMember($auth_member, $post, $additionalInfo));
            }
            $this->logger->debug("Posts are: ", array_map(function (Post $post) {
                return $post->toObject();
            }, $posts));

            $response = $this->view->render($response, "member-page.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $auth_member,
              'member' => $member,
              'on_own_profile' => $on_own_profile,
              'relationship' => $relationship,
              'posts' => $posts,
              'posts_can_edit' => $posts_can_edit,
              'posts_comment_count' => $posts_comment_count,
              'submit_url' => $this->router->pathFor('post-create', ['page_id' => $page->getPageId()])
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('profile');

    /*
     * Perform the update to a member's profile information.
     */
    $this->get('/update', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        $auth_status = $this->sessionService->isAuthenticated();
        if ($auth_status && $member->getMemberId() == $this->sessionService->getAuthenticatedMember()->getMemberId()){
            $this->memberService->populateInterestsForMember($member);
            $member = $this->memberService->populateProfessionForMember($member);
            $member = $this->memberService->populateRegionForMember($member);
            $response = $this->view->render($response, "profile-update.html", [
              'is_authenticated' => $auth_status,
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
    })->setname('member_update');

    /*
    * View and confirm or delete pending relationship requests;
    */
    $this->get('/requests', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status && $member->getMemberId() == $auth_member->getMemberId()){
            $this->logger->addInfo("Member page for $username");
            $pending_reqs = $this->relationshipService->getPendingRelRequests($member);
            $response = $this->view->render($response, "member-requests.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $auth_member,
              'pending_reqs' => $pending_reqs
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('pending');

    /*
     * Update a member's profile details: Name, email, date of birth
     */
    $this->post('/update_details', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status && $member->getMemberId() == $this->sessionService->getAuthenticatedMember()->getMemberId()){
            $params = $request->getParsedBody();
            $res = $this->memberService->updatePowonMember($member, $params);
            return $response->withRedirect("/members/$username");
        }
        return $response->withRedirect('/'); // Permission denied
    })->setname('member_details_update');

    /*
     * Update a member's profile picture
     */
    $this->post('/update_picture', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        $file = $request->getUploadedFiles()['fileToUpload'];
        if ($auth_status && $member->getMemberId() == $this->sessionService->getAuthenticatedMember()->getMemberId()){
            $params = $request->getParsedBody();
            $success = $this->memberService->updateProfilePic($member, $file);
            //TODO Flash message
            return $response->withRedirect('update');
        }
        return $response->withRedirect('/'); // Permission denied
    })->setname('member_pic_update');

    $this->post('/add', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $rel_type = $request->getParsedBody()["rel_type"];
        $this->relationshipService->requestRelationship($auth_member, $member, $rel_type);
        //TODO message flash
        return $response->withRedirect("/members/$username");
    })->setname('addRel');

    $this->post('/confirm', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $this->relationshipService->confirmRelationship($member, $auth_member);
        //TODO message flash
        return $response->withRedirect("/members/$my_username/requests");
    })->setname('confirmRel');

    $this->post('/delete', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $my_username = $auth_member->getUsername();
        var_dump($rel_type);
        $this->relationshipService->deleteRelationship($auth_member, $member);
        //TODO message flash
        return $response->withRedirect("/members/$my_username/requests");
    })->setname('deleteRel');

    /*
    * View all friends for a given member. Can delete friends if on own page
    */
    $this->get('/friends', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status){
            $this->logger->addInfo("Member page for $username");
            $related_members = $this->relationshipService->getRelatedMembers($member, 'F');
            $on_own_page = $member == $auth_member;
            $response = $this->view->render($response, "relationship.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'on_own_page' => $on_own_page,
              'related_members' => $related_members,
              'relationship' => "Friends"
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('friends');

    /*
    * View all immediate family for a given member. Can delete family if on own page
    */
    $this->get('/immediate_family', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status){
            $this->logger->addInfo("Member page for $username");
            $related_members = $this->relationshipService->getRelatedMembers($member, 'I');
            $on_own_page = $member == $auth_member;
            $response = $this->view->render($response, "relationship.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'on_own_page' => $on_own_page,
              'related_members' => $related_members,
              'relationship' => "Immediate Family"
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('imm_fam');

    /*
    * View all extended family for a given member.
    * Can delete family members if on own page.
    */
    $this->get('/extended_family', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status){
            $this->logger->addInfo("Member page for $username");
            $related_members = $this->relationshipService->getRelatedMembers($member, 'E');
            $on_own_page = $member == $auth_member;
            $response = $this->view->render($response, "relationship.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'on_own_page' => $on_own_page,
              'related_members' => $related_members,
              'relationship' => "Extended Family"
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('ext_fam');

    /*
    * View all colleagues for a given member.
    * Can delete colleagues if on own page.
    */
    $this->get('/colleagues', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status){
            $this->logger->addInfo("Member page for $username");
            $related_members = $this->relationshipService->getRelatedMembers($member, 'C');
            $on_own_page = $member == $auth_member;
            $response = $this->view->render($response, "relationship.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'on_own_page' => $on_own_page,
              'related_members' => $related_members,
              'relationship' => "Colleagues"
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('colleagues');
});
