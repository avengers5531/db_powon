<?php
use Powon\Entity\Post;
use Powon\Services\PostService;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

$app->group('/post', function () use ($container) {

    /**
     * @var \Powon\Services\GroupService $groupService
     */
    $groupService = $container->groupService;

    /**
     * @var \Powon\Services\SessionService $sessionService
     */
    $sessionService = $container->sessionService;

    /**
     * @var \Powon\Services\GroupPageService $groupPageService
     */
    $groupPageService = $container->groupPageService;

    /**
     * @var \Powon\Services\MemberPageService $memberPageService
     */
    $memberPageService = $container->memberPageService;

    /**
     * @var \Powon\Services\PostService $postService
     */
    $postService = $container->postService;

    /**
     * @var \Powon\Services\MemberService $memberService
     */
    $memberService = $container->memberService;

    $getAdditionalInfo = function ($page_id)
    use ($postService, $memberPageService, $groupPageService, $groupService, $container)
    {
        $additional_info = [];
        $memberPage = $memberPageService->getMemberPageByPageId($page_id);
        if ($memberPage) {
            $container->logger->debug('Member page is: ', $memberPage->toObject());
            $additional_info['memberPage'] = $memberPage;
        } else { // must be a group.
            $groupPage = $groupPageService->getPageById($page_id);
            $additional_info['groupPage'] = $groupPage;
            $container->logger->debug('Group Page for post is ', ['id' => $groupPage->getPageId()]);
            $group = $groupService->getGroupById($groupPage->getPageGroupId());
            $additional_info['group'] = $group;
            $container->logger->debug('Group for post is ', $group->toObject());
        }
        return $additional_info;
    };

    $getRedirectPathToPage = function($additional_info, $page_id)
    use ($memberService, $container)
    {
        if (isset($additional_info['memberPage'])) {
            $memberPage = $additional_info['memberPage'];
            $member = $memberService->getMemberById($memberPage->getMemberId());
            if (!$member) {
                $redirectPath = '/';
            }
            $redirectPath = $container->router->pathFor('profile', ['username' => $member->getUsername()]);
        } else {
            $redirectPath =  $container->router->pathFor('view-group-page', ['page_id' => $page_id]);
        }
        return $redirectPath;
    };

    // Routes for posts.

    // GET route for /post/{post_id} (returns the post profile form)
    $this->get('/{post_id}', function (Request $request, Response $response)
    use ($sessionService, $postService, $getAdditionalInfo)
    {
        $post_id = $request->getAttribute('post_id');
        $current_member = $sessionService->getAuthenticatedMember();
        if (!$current_member) {
            $body = $response->getBody();
            $body->write('Middleware to check authentication coming soon!');
            return $response->withBody($body)->withStatus(403);
        }
        $post = $postService->getPostById($post_id);
        if (!$post) {
            return $response->withStatus(404);
        }
        $additional_info = $getAdditionalInfo($post->getPageId());
        if (isset($additional_info['memberPage'])) {
            $menu = ['active' => 'profile'];
        } else { // must be a group.
            $menu = ['active' => 'groups'];
        }
        $can_edit = $postService->canMemberEditPost($current_member, $post, $additional_info);

        // have to set the right permission for the post for the current user:
        $member_permission = $postService->getCommentPermissionForMember($current_member, $post, $additional_info);
        $post->setCommentPermission($member_permission);

        $comments = $postService->getPostCommentsAccessibleToMember($current_member, $post, $additional_info);
        $this->logger->debug('Comments:', $comments);
        $comments_can_edit = [];
        $comments_children_count = [];
        foreach ($comments as &$comment) {
            $id = $comment->getPostId();
            $comments_can_edit[$id] = $postService->canMemberEditPost($current_member, $comment, $additional_info);
            $child_count = count($postService->getPostCommentsAccessibleToMember($current_member, $comment, $additional_info));
            $comments_children_count[$id] = $child_count;
        }
        // get messages from flash
        $post_success_message = null;
        $post_error_message = null;
        $sessData = $sessionService->getSession()->getSessionData();
        if (isset($sessData['flash'])) {
            $flash = $sessData['flash'];
            if (isset($flash['post_error_message'])) {
                $post_error_message = $flash['post_error_message'];
            } elseif (isset($flash['post_success_message'])) {
                $post_success_message = $flash['post_success_message'];
            }
            $sessionService->getSession()->removeSessionData('flash');
        }

        return $this->view->render($response, 'post-profile.html', [
            'current_member' => $current_member,
            'menu' => $menu,
            'post' => $post,
            'can_edit' => $can_edit,
            'comments' => $comments,
            'comments_can_edit' => $comments_can_edit,
            'comments_children_count' => $comments_children_count,
            'additional_info' => $additional_info,
            'submit_url' => $this->router->pathFor('comment-create', ['post_id' => $post_id]),
            'post_success_message' => $post_success_message,
            'post_error_message' => $post_error_message
        ]);
    })->setName('view-post');

    $this->get('/{post_id}/edit', function(Request $request, Response $response)
    use ($sessionService, $postService, $getAdditionalInfo)
    {
        $post_id = $request->getAttribute('post_id');
        $current_member = $sessionService->getAuthenticatedMember();
        $post = $postService->getPostById($post_id);
        if (!$post) {
            return $response->withStatus(404);
        }
        $additional_info = $getAdditionalInfo($post->getPageId());
        if (!$postService->canMemberEditPost($current_member, $post, $additional_info)) {
            return $response->withStatus(403);
        }
        $parent_post_id = $post->getParentPostId();
        $can_add_content = true;
        if ($parent_post_id) {
            $parent_post = $postService->getPostById($parent_post_id);
            $comment_permission = $postService->getCommentPermissionForMember($current_member, $parent_post, $additional_info);
            if ($comment_permission !== Post::PERMISSION_ADD_CONTENT) {
                $can_add_content = false;
            }
        }
        $acl = $postService->getCustomAccessListForPost($post);
        $custom_access_list = array_map(function ($access) {
            return [
                'member_id' => $access['member']->getMemberId(),
                'username' => $access['member']->getUsername(),
                'permission' => $access['permission']
            ];
        }, $acl);
        $response = $this->view->render($response, 'post-update.html', [
            'mode' => 'update',
            'post' => $post,
            'can_add_content' => $can_add_content,
            'custom_access_list' => $custom_access_list,
            'current_member' => $current_member,
            'submit_url' => $this->router->pathFor('post-update', ['post_id' => $post_id])
        ]);

        return $response;
    })->setName('update-post');

    $this->post('/create/{page_id}', function(Request $request, Response $response)
    use ($postService, $sessionService, $getAdditionalInfo, $memberService, $getRedirectPathToPage)
    {
        $page_id = $request->getAttribute('page_id');
        $params = $request->getParsedBody();
        $this->logger->debug('Request to create a post contains.', $params);
        $uploaded_files = $request->getUploadedFiles();
        if (isset($uploaded_files[PostService::FIELD_FILE])) {
            $params[PostService::FIELD_FILE] = $uploaded_files[PostService::FIELD_FILE];
        }
        $additional_info = $getAdditionalInfo($page_id);
        $res = $postService->createPost($sessionService->getAuthenticatedMember(), $params, $additional_info);
        $sess = $sessionService->getSession();
        if ($res['success']) {
            $sess->addSessionData('flash', [
                'post_success_message' => $res['message']
            ]);
        } else {
            $sess->addSessionData('flash', [
                'post_error_message' => $res['message']
            ]);
        }
        if (isset($additional_info['memberPage'])) {
            $memberPage = $additional_info['memberPage'];
            $member = $memberService->getMemberById($memberPage->getMemberId());
            if (!$member) {
                $redirectPath = '/';
            }
            $redirectPath = $this->router->pathFor('profile', ['username' => $member->getUsername()]);
        } else {
           $redirectPath =  $this->router->pathFor('view-group-page', ['page_id' => $page_id]);
        }
        return $response->withRedirect($redirectPath);
    })->setName('post-create');

    $this->post('/{post_id}/comment/create', function(Request $request, Response $response)
    use ($postService, $sessionService, $getAdditionalInfo)
    {
        $parent_post_id = $request->getAttribute('post_id');
        $parent_post = $postService->getPostById($parent_post_id);
        if (!$parent_post) {
            return $response->withStatus(404);
        }
        $page_id = $parent_post->getPageId();
        $additional_info = $getAdditionalInfo($page_id);
        $permission = $postService->getCommentPermissionForMember($sessionService->getAuthenticatedMember(),
            $parent_post, $additional_info);
        if ($permission !== Post::PERMISSION_COMMENT &&
        $permission !== Post::PERMISSION_ADD_CONTENT) {
            return $response->withStatus(403);
        }
        $params = $request->getParsedBody();
        $uploaded_files = $request->getUploadedFiles();
        if (isset($uploaded_files[PostService::FIELD_FILE])) {
            $params[PostService::FIELD_FILE] = $uploaded_files[PostService::FIELD_FILE];
        }
        $params[PostService::FIELD_PARENT] = $parent_post_id;
        $res = $postService->createPost($sessionService->getAuthenticatedMember(), $params, $additional_info);
        $sess = $sessionService->getSession();
        if ($res['success']) {
            $sess->addSessionData('flash', [
                'post_success_message' => $res['message']
            ]);
        } else {
            $sess->addSessionData('flash', [
                'post_error_message' => $res['message']
            ]);
        }
        return $response->withRedirect($this->router->pathFor('view-post', ['post_id' => $parent_post_id]));
    })->setName('comment-create');

    $this->post('/{post_id}/delete', function(Request $request, Response $response)
    use ($postService, $sessionService, $getAdditionalInfo, $getRedirectPathToPage)
    {
        $post_id = $request->getAttribute('post_id');
        $post = $postService->getPostById($post_id);
        if (!$post) {
            return $response->withStatus(404);
        }
        $additional_info = $getAdditionalInfo($post->getPageId());
        $res = $postService->deletePost($post_id, $sessionService->getAuthenticatedMember(), $additional_info);
        $sess = $sessionService->getSession();
        if ($res['success']) {
            $sess->addSessionData('flash', [
                'post_success_message' => $res['message']
            ]);
        } else {
            $sess->addSessionData('flash', [
                'post_error_message' => $res['message']
            ]);
        }
        $redirectPath = $getRedirectPathToPage($additional_info, $post->getPageId());
        return $response->withRedirect($redirectPath);
    })->setName('post-delete');

    // TODO update route

    $this->post('/{post_id}/update', function (Request $request, Response $response)
    use ($postService, $sessionService, $getAdditionalInfo, $getRedirectPathToPage)
    {
        $post_id = $request->getAttribute('post_id');
        $params = $request->getParsedBody();
        $post = $postService->getPostById($post_id);
        if (!$post) {
           return $response->withStatus(404);
        }
        // get uploaded files
        $uploaded_files = $request->getUploadedFiles();
        if (isset($uploaded_files[PostService::FIELD_FILE]) &&
            filesize($uploaded_files[PostService::FIELD_FILE]->file) > 0) {
            $params[PostService::FIELD_FILE] = $uploaded_files[PostService::FIELD_FILE];
        }
        $additional_info = $getAdditionalInfo($post->getPageId());
        // set parent post if any:
        $parent_post_id = $post->getParentPostId();
        if ($parent_post_id) {
            $params[PostService::FIELD_PARENT] = $parent_post_id;
        }
        $res = $postService->updatePost($post_id, $params, $sessionService->getAuthenticatedMember(), $additional_info);
        $sess = $sessionService->getSession();
        if ($res['success']) {
            $sess->addSessionData('flash', [
                'post_success_message' => $res['message']
            ]);
        } else {
            $sess->addSessionData('flash', [
                'post_error_message' => $res['message']
            ]);
        }
        return $response->withRedirect($this->router->pathFor('view-post', ['post_id' => $post_id]));
    })->setName('post-update');


}); // TODO add authenticated check middleware.

