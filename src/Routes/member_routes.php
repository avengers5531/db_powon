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
            $on_own_profile = $member == $auth_member? true : false;
            $response = $this->view->render($response, "member-page.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'on_own_profile' => $on_own_profile
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
            $response = $this->view->render($response, "profile-update.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
            ]);
            return $response;
        }
        return $response->withRedirect('/'); // Permission denied
    })->setname('member_update');

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

});
