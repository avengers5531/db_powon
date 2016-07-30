<?php
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

    // Routes for posts.

    // GET route for /post/{post_id} (returns the post profile form)
    $this->get('/{post_id}', function (Request $request, Response $response)
    use ($groupService, $sessionService, $memberPageService, $groupPageService, $postService) {
        $post_id = $request->getAttribute('post_id');
        $post = $postService->getPostById($post_id);
        if (!$post) {
            return $response->withStatus(404);
        }
        $current_member = $sessionService->getAuthenticatedMember();
        $additional_info = [];
        $group = null;
        $groupPage = null;
        // build additional info:
        $memberPage = $memberPageService->getMemberPageByPageId($post->getPageId());
        if ($memberPage) {
            $this->logger->debug('Member page is: ', $memberPage->toObject());
            $additional_info['memberPage'] = $memberPage;
            $menu = ['active' => 'profile'];
        } else { // must be a group.
            $groupPage = $groupPageService->getPageById($post->getPageId());
            $additional_info['groupPage'] = $groupPage;
            $this->logger->debug('Group Page is ', ['id' => $groupPage->getPageId()]);
            $group = $groupService->getGroupById($groupPage->getPageGroupId());
            $additional_info['group'] = $group;
            $this->logger->debug('Group is ', $group->toObject());
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
            'memberPage' => $memberPage,
            'groupPage' => $groupPage,
            'group' => $group
        ]);

    })->setName('view-post');

}); // TODO add authenticated check middleware.
