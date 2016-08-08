<?php
use Powon\Entity\Post;
use Powon\Services\GiftWantedService;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;
use \Powon\Entity\Member;

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
            if (!$member) {
                return $response->withStatus(404);
            }
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
            $member_id = $member->getMemberId();
            $this->logger->addInfo("Attempt to access member id: $member_id");
            $page = $this->memberPageService->getMemberPageByMemberId($member->getMemberId());
            $wishlist = $this->giftWantedService->getWishListById($member_id);
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
            // consume flash message if any
            $post_success_message = null;
            $post_error_message = null;
            $session = $this->sessionService->getSession();
            $sessData = $session->getSessionData();
            if (isset($sessData['flash'])) {
                $flash = $sessData['flash'];
                if (isset($flash['post_error_message']))
                    $post_error_message = $flash['post_error_message'];
                elseif (isset($flash['post_success_message']))
                    $post_success_message = $flash['post_success_message'];
                $session->removeSessionData('flash');
            }
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
              'submit_url' => $this->router->pathFor('post-create', ['page_id' => $page->getPageId()]),
              'post_success_message' => $post_success_message,
              'post_error_message' => $post_error_message,
                'wishlist' => $wishlist
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
            $wishlist_str = array_map(function($it) {
                return $it->getGiftName();
            }, $this->giftWantedService->getWishListById($member->getMemberId()));
            $this->logger->debug("$username has wish list of:" , $wishlist_str);
            $page = $this->memberPageService->getMemberPageByMemberId($member->getMemberId());
            $gift_inventory = $this->giftWantedService->getGiftInventory();
            $response = $this->view->render($response, "profile-update.html", [
              'is_authenticated' => $auth_status,
              'menu' => [
                'active' => 'profile'
              ],
              'current_member' => $this->sessionService->getAuthenticatedMember(),
              'member' => $member,
              'page' => $page,
              'interests' => $this->memberService->getAllInterests(),
              'professions' => $this->memberService->getAllProfessions(),
              'wishlist_str' => $wishlist_str,
              'gift_inventory' => $gift_inventory
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
        $member = $this->memberService->getMemberByUsername($username);
        $params = $request->getParsedBody();
        if ($params['attribute'] === 'details'){
            $res = $this->memberService->updatePowonMember($member, $params);
        }
        elseif ($params['attribute'] === 'interest') {
            $res = $this->memberService->updateMemberInterests($member, $params);
        }
        elseif ($params['attribute'] === 'profession') {
            $res = $this->memberService->updateMemberProfession($member, $params);
        }
        elseif ($params['attribute'] === 'region') {
            $res = $this->memberService->updateMemberRegion($member, $params);
        }
        elseif ($params['attribute'] === 'access') {
            $page = $this->memberPageService->getMemberPageByMemberId($member->getMemberId());
            $res = $this->memberService->updateMemberAccess($member, $page, $params);
        }
        return $response->withRedirect("/members/$username/update");
        var_dump($params);
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

    /*
     * View all invoices (paid or unpaid)
     */
    $this->get('/member-invoice', function(Request $request, Response $response){
        $auth_status = $this->sessionService->isAuthenticated();
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        if ($auth_status) {
            $this->logger->addInfo("Invoice page for $username");
            $id = $member->getMemberId();
            $invoices = $this->invoiceService->getInvoiceByMember($id);
            $response = $this->view->render($response, "member-invoice.html", [
                'is_authenticated' => $auth_status,
                'current_member' => $this->sessionService->getAuthenticatedMember(),
                'invoices' => $invoices
            ]);
            return $response;
        }
        return $response->withRedirect('/');
    })->setname('member-invoice');


    /*
    * Pay an invoice
    */
    $this->post('/invoice-payment/{invoice_id}', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $invoice_id = $request->getAttribute('invoice_id');
        $auth_status = $this->sessionService->isAuthenticated();
        $member = $this->memberService->getMemberByUsername($username);
        $this->logger->addInfo("attempt to pay invoice " . $invoice_id);
        if ($auth_status && $member->getMemberId() == $this->sessionService->getAuthenticatedMember()->getMemberId()){
            $params = $request->getParsedBody();
            $res = $this->invoiceService->payInvoice($invoice_id, $member);
            return $response->withRedirect("/members/$username/member-invoice");
        }
        return $response->withRedirect('/members/$username'); // Permission denied
    })->setname('invoice-payment');

    $this->get('/update_password', function(Request $request, Response $response) {
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        $current_member = $this->sessionService->getAuthenticatedMember();
        if ($member->getMemberId() == $current_member->getMemberId() || $current_member->isAdmin()) {
            // consume flash message if any
            $post_error_message = null;
            $session = $this->sessionService->getSession();
            $sessData = $session->getSessionData();
            if (isset($sessData['flash'])) {
                $flash = $sessData['flash'];
                if (isset($flash['post_error_message']))
                    $post_error_message = $flash['post_error_message'];
                $session->removeSessionData('flash');
            }
            return $this->view->render($response, 'password-update.html', [
                'current_member' => $current_member,
                'member' => $member,
                'menu' => ['active' => 'profile'],
                'post_error_message' => $post_error_message
            ]);
        }
        return $response->withStatus(403); // forbidden
    })->setName('member_password_update_get');

    /*
     * Update a member's password
     */
    $this->post('/update_password', function(Request $request, Response $response){
        $username = $request->getAttribute('username');
        $member = $this->memberService->getMemberByUsername($username);
        $current_member = $this->sessionService->getAuthenticatedMember();
        if ($member->getMemberId() == $current_member->getMemberId() || $current_member->isAdmin()){
            $params = $request->getParsedBody();
            $res = $this->memberService->updatePassword($member, $current_member, $params);
            $session = $this->sessionService->getSession();
            $session->addSessionData('flash', [
                'post_'. ($res['success'] ? 'success' : 'error') . '_message' => $res['message']
            ]);
            return $response->withRedirect($this->router->pathFor($res['success']?'profile':'member_password_update_get',['username' => $username]));
        }
        return $response->withStatus(403); // Permission denied
    })->setname('member_password_update_post');

    // Give a gift to member:
    $this->post('/give-gift/{gift_name}', function (Request $request, Response $response) {
        $giftWantedService = $this->giftWantedService;
        $username = $request->getAttribute('username');
        $gift_name = $request->getAttribute('gift_name');
        $current_member = $this->sessionService->getAuthenticatedMember();
        $member = $this->memberService->getMemberByUsername($username);
        $success = false;
        if (!$member)
            return $response->withStatus(404);
        if ($current_member->getMemberId() == $member->getMemberId()) {
            $msg = 'You cannot give a gift to yourself.';
            $success = false;
        } else {
            $success = $giftWantedService->giveGift($current_member, $member, $gift_name);
            if ($success)
                $msg = 'The Gift was sent!';
            else
                $msg = 'Unable to send gift.';
        }
        $session = $this->sessionService->getSession();
        $session->addSessionData('flash', [
            'post_'. ($success ? 'success' : 'error') . '_message' => $msg
        ]);
        return $response->withRedirect($this->router->pathFor('profile', ['username' => $username]));
    })->setName('give-gift');

    // update gift wish list
    $this->post('/update_wish_list', function (Request $request, Response $response) {
        /**
         * @var GiftWantedService $giftWantedService
         */
        $giftWantedService = $this->giftWantedService;
        $username = $request->getAttribute('username');
        $gift_name = $request->getAttribute('gift_name');
        $params = $request->getParsedBody();
        $gifts = (isset($params[GiftWantedService::FIELD_GIFT])) ? $params[GiftWantedService::FIELD_GIFT] : [];
        $current_member = $this->sessionService->getAuthenticatedMember();
        $member = $this->memberService->getMemberByUsername($username);
        if ($current_member->getMemberId() != $member->getMemberId() && !$current_member->isAdmin()) {
            return $response->withStatus(403);
        }
        $success = $giftWantedService->updateWishList($member->getMemberId(), $gifts);
        $session = $this->sessionService->getSession();
        $session->addSessionData('flash', [
            'post_'. ($success ? 'success' : 'error') . '_message' => ($success?'Updated wish list.' : 'failed to update wish list.')
        ]);
        return $response->withRedirect($this->router->pathFor('member_update', ['username' => $username]));
    })->setName('update-wish-list');

})->add(function (Request $request, Response $response, Callable $next) use ($container) {
    $sessionService = $container['sessionService'];
    if (!$sessionService->isAuthenticated()) {
        return $response->withStatus(403);
    }
    return $next($request, $response);
});
