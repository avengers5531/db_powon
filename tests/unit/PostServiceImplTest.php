<?php

use PHPUnit\Framework\TestCase;
use Powon\Entity\Post;
use Powon\Services\PostService;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\PostDaoStub;


class PostServiceImplTest extends TestCase
{

    /**
     * @var PostService $postService
     */
    private $postService;

    public function setUp()
    {
        parent::setUp();
        $dao = new PostDaoStub();
        $dao->posts = array(
         [
             'post_id' => 1,
             'post_date_created' => '2016-06-01 10:11:04',
             'post_type' => 'T',
             'path_to_resource' => 'members/admin',
             'post_body' => 'my favourite fish is a happy fish',
             'comment_permission' => 'A',
             'page_id' => 1,
             'author_id' => 1,
             'parent_post' => null,
         ],
         [
             'post_id' => 2,
             'post_date_created' => '2016-06-03 12:44:05',
             'post_type' => 'T',
             'path_to_resource' => 'members/admin',
             'post_body' => 'omfg ME TOO love those lil guys',
             'comment_permission' => 'C',
             'page_id' => 1,
             'author_id' => 2,
             'parent_post' => null,

         ],
         [
             'post_id' => 3,
             'post_date_created' => '2016-06-04',
             'post_type' => 'T',
             'path_to_resource' => 'members/admin',
             'post_body' => 'lets be pals',
             'comment_permission' => 'V',
             'page_id' => 1,
             'author_id' => 1,
             'parent_post' => null
        ],
        [
            'post_id' => 4,
            'post_date_created' => '2016-07-31 12:12:12',
            'post_type' => 'V',
            'path_to_resource' => 'cQtFz173bAk', // youtube video
            'post_body' => 'This is a comment video!',
            'comment_permission' => 'T',
            'page_id' => 1,
            'author_id' => 2,
            'parent_post' => 1
        ]);
        $dao->custom_access = [
            [
                'member_id' => 1,
                'comment_permission' => 'A',
                'post_id' => 4
            ],
            [
                'member_id' => 3,
                'comment_permission' => 'V',
                'post_id' => 4
            ]
        ];
        $logger = new LoggerStub();
        $memberDao = new \Powon\Test\Stub\MemberDaoStub();
        $memberDao->members = array(
            [
                'member_id' => 1,
                'username' => 'User1',
                'first_name' => 'First',
                'last_name' => 'Last',
                'user_email' => 'test_user1@mail.ca',
                'date_of_birth' => '1989-12-13',
                'is_admin' => 'N',
                'status' => 'A',
                'profile_picture' => '/assets/images/profile/lionfish.jpg'
            ],
            [
                'member_id' => 2,
                'username' => 'User2',
                'first_name' => 'First2',
                'last_name' => 'Last2',
                'user_email' => 'test_user2@mail.ca',
                'date_of_birth' => '1994-02-11',
                'is_admin' => 'N',
                'status' => 'A',
                'profile_picture' => '/assets/images/profile/lionfish.jpg'
            ]);
        $this->postService = new \Powon\Services\Implementation\PostServiceImpl($logger,$dao, $memberDao);
    }

    public function testGetPostById() {
        $res = $this->postService->getPostById(2);
        $this->assertEquals($res->getPostBody(), 'omfg ME TOO love those lil guys');
    }

    public function testGetPostsByPage(){
        $res = $this->postService->getPostsByPage(1);
        $this->assertEquals(3, count($res));
    }

    public function testGetPostsByAuthor(){
        $res = $this->postService->getPostsByAuthor(1);
        $this->assertEquals(count($res), 2);
    }

    public function testCreateNewPost(){
        $res = $this->postService->createNewPost('t', '/members/admin', 'im a new post!', 'p', '2', '1', null);
        $this->assertTrue($res['success']);
    }

    public function testGetCustomAccesListForPost() {
        $post_1 = $this->postService->getPostById(1);
        $this->assertNotNull($post_1);
        $this->assertEmpty($this->postService->getCustomAccessListForPost($post_1));
        $post_4 = $this->postService->getPostById(4);
        $res = $this->postService->getCustomAccessListForPost($post_4);
        $this->assertEquals(2, count($res));
        $custom = $res[0];
        $this->assertEquals(1, $custom['member']->getMemberId());
        $this->assertEquals(Post::PERMISSION_ADD_CONTENT, $custom['permission']);
    }

    public function testCreateUpdatePost() {
        $params = [
            PostService::FIELD_BODY => 'Test post',
            PostService::FIELD_TYPE => Post::TYPE_TEXT,
            PostService::FIELD_PERMISSION_TYPE => Post::PERMISSION_COMMENT
        ];
        $memberPage = new \Powon\Entity\MemberPage([
            'page_id' => 44,
            'page_title' => 'A member\'s page',
            'date_created' => '1999-09-12 00:12:01',
            'page_access' => 15,
            'member_id' => 2
        ]);
        $author = new \Powon\Entity\Member([
            'member_id' => 2,
            'username' => 'User2',
            'first_name' => 'First2',
            'last_name' => 'Last2',
            'user_email' => 'test_user2@mail.ca',
            'date_of_birth' => '1994-02-11',
            'is_admin' => 'N',
            'status' => 'A',
            'profile_picture' => '/assets/images/profile/lionfish.jpg'
        ]);
        $res = $this->postService->createPost($author, $params, ['memberPage' => $memberPage]);
        $this->assertTrue($res['success']);
        $posts = $this->postService->getPostsByAuthor(2);
        $this->assertEquals(2, count($posts));
        /**
         * @var Post $post
         */
        $post = $posts[1];
        $id = $post->getPostId();
        $this->assertEquals('Test post', $post->getPostBody());
        $this->assertEquals(Post::PERMISSION_COMMENT, $post->getCommentPermission());
        $this->assertEquals(Post::TYPE_TEXT, $post->getPostType());
        $this->assertNull($post->getPathToResource());

        $params[PostService::FIELD_BODY] = 'Updated post body.';
        $params[PostService::FIELD_PATH] = 'cQtFz173bAk';
        $params[PostService::FIELD_TYPE] = Post::TYPE_VIDEO;
        $res = $this->postService->updatePost($id, $params, $author, ['memberPage' => $memberPage]);
        $this->assertTrue($res['success']);
        $updated = $this->postService->getPostById($id);
        $this->assertNotNull($updated);
        $this->assertEquals('Updated post body.', $updated->getPostBody());
        $this->assertEquals(Post::PERMISSION_COMMENT, $updated->getCommentPermission());
        $this->assertEquals(Post::TYPE_VIDEO, $updated->getPostType());
        $this->assertEquals('cQtFz173bAk', $updated->getPathToResource());

    }
}
