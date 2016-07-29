<?php
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
            if ($member == $auth_member){
                $on_own_profile = true;
            }
            else{
                $on_own_profile = false;
                $relationship = $this->relationshipService->checkRelationship($member, $auth_member);
            }
            $response = $this->view->render($response, "member-page.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'on_own_profile' => $on_own_profile,
              'relationship' => $relationship,
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
        if ($auth_status && $member == $this->sessionService->getAuthenticatedMember()){
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
        if ($auth_status && $member == $auth_member){
            $this->logger->addInfo("Member page for $username");
            $pending_reqs = $this->relationshipService->getPendingRelRequests($member);
            $response = $this->view->render($response, "member-requests.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
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
        if ($auth_status && $member == $this->sessionService->getAuthenticatedMember()){
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
        if ($auth_status && $member == $this->sessionService->getAuthenticatedMember()){
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
        return $response->withRedirect("/members/$username");
    })->setname('confirmRel');

    $this->post('/delete', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        $auth_member = $this->sessionService->getAuthenticatedMember();
        var_dump($rel_type);
        // $this->relationshipService->requestRelationship($auth_member, $member, $rel_type);
        //TODO message flash
        return $response->withRedirect("/members/$username");
    })->setname('deleteRel');

});
