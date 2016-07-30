<?php
use Powon\Entity\Post;
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

    $getAdditionalInfo = function (Post $post)
    use ($postService, $memberPageService, $groupPageService, $groupService)
    {
        $additional_info = [];
        $memberPage = $memberPageService->getMemberPageByPageId($post->getPageId());
        if ($memberPage) {
            $this->logger->debug('Member page is: ', $memberPage->toObject());
            $additional_info['memberPage'] = $memberPage;
        } else { // must be a group.
            $groupPage = $groupPageService->getPageById($post->getPageId());
            $additional_info['groupPage'] = $groupPage;
            $this->logger->debug('Group Page is ', ['id' => $groupPage->getPageId()]);
            $group = $groupService->getGroupById($groupPage->getPageGroupId());
            $additional_info['group'] = $group;
            $this->logger->debug('Group is ', $group->toObject());
        }
        return $additional_info;
    };

    // Routes for posts.

    // GET route for /post/{post_id} (returns the post profile form)
    $this->get('/{post_id}', function (Request $request, Response $response)
    use ($sessionService, $postService, $getAdditionalInfo)
    {
        $post_id = $request->getAttribute('post_id');
        $post = $postService->getPostById($post_id);
        if (!$post) {
            return $response->withStatus(404);
        }
        $current_member = $sessionService->getAuthenticatedMember();
        $additional_info = $getAdditionalInfo($post);
        if (isset($additional_info['memberPage'])) {
            $menu = ['active' => 'profile'];
        } else { // must be a group.
            $menu = ['active' => 'groups'];
        }
        $can_edit = $postService->canMemberEditPost($current_member, $post, $additional_info);

        $comments = $postService->getPostCommentsAccessibleToMember($current_member, $post, $additional_info);
        $this->logger->debug('Comments:', $comments);
        $comments_can_edit = [];
        foreach ($comments as &$comment) {
            $id = $comment->getPostId();
            $comments_can_edit[$id] = $postService->canMemberEditPost($current_member, $comment, $additional_info);
        }

        return $this->view->render($response, 'post-profile.html', [
            'current_member' => $current_member,
            'menu' => $menu,
            'post' => $post,
            'can_edit' => $can_edit,
            'comments' => $comments,
            'comments_can_edit' => $comments_can_edit,
            'additional_info' => $additional_info
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
        $additional_info = $getAdditionalInfo($post);
        if (!$postService->canMemberEditPost($current_member, $post, $additional_info)) {
            return $response->withStatus(403);
        }
        $parent_post_id = $post->getParentPostId();
        $can_add_content = true;
        if ($parent_post_id) {
            $parent_post = $postService->getPostById($parent_post_id);
            $comment_permission = $postService->getCommentPermissionForMember($current_member, $parent_post);
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
            'current_member' => $current_member
        ]);

        return $response;
    })->setName('update-post');

}); // TODO add authenticated check middleware.
