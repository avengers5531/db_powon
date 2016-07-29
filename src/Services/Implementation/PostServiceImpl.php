<?php

namespace Powon\Services\Implementation;

use Powon\Dao\MemberDAO;
use Powon\Entity\Member;
use Powon\Entity\Post;
use Powon\Services\PostService;
use Powon\Dao\PostDAO;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;
use Slim\Http\UploadedFile;

class PostServiceImpl implements PostService
{
    /**
     * @var PostDAO
     */
    private $postDAO;

    /**
     * @var MemberDAO
     */
    private $memberDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger,
                                PostDAO $dao,
                                MemberDAO $memberDAO)
    {
        $this->postDAO = $dao;
        $this->log = $logger;
        $this->memberDAO = $memberDAO;
    }

    /**
    *@param $post_id int
    *@return Post|null entity
    */
    public function getPostById($post_id){
      try {
          $post = $this->postDAO->getPostById($post_id);
          $this->populatePostAuthor($post);
          return $post;
      } catch (\PDOException $ex) {
        $this->log->error("A pdo exception occured: " . $ex->getMessage());
        return null;
      }
    }

    /**
    *@param $page_id int
    *@return [Post]
    */

    public function getPostsByPage($page_id){
      try {
          $posts = $this->postDAO->getPostsByPage($page_id);
          foreach ($posts as &$post) {
              $this->populatePostAuthor($post);
          }
          return $posts;
      } catch (\PDOException $ex) {
          $this->log->error("A pdo exception occurred: " . $ex->getMessage());
          return [];
      }
    }

    /**
    *@param $author_id int
    *@return [Post]
    */
    public function getPostsByAuthor($author_id){
      try {
          $posts = $this->postDAO->getPostsByAuthor($author_id);
          if (count($posts) > 0) {
              $this->populatePostAuthor($posts[0]);
              $author = $posts[0]->getAuthor();
              for ($i = 1; $i < count($posts); $i++) {
                  $posts[$i]->setAuthor($author);
              }
          }
          return $posts;
      } catch (\PDOException $ex) {
          $this->log->error("A pdo exception occurred: " . $ex->getMessage());
          return [];
      }
    }


    /**
     * @param $post_type string
     * @param $path_to_resource string
     * @param $post_body string
     * @param $comment_permission string
     * @param $page_id int
     * @param $author_id int
     * @param int|string $parent_post
     * @return mixed array('success': bool, 'message':string, 'post_id': int)
     */
      public function createNewPost($post_type, $path_to_resource, $post_body,
                                    $comment_permission, $page_id, $author_id, $parent_post) {
          //get current date when creating a new post
          $date = date("YYYY-MM-DD");
          $data = array(
              'post_date_created' => $date,
              'post_type' => $post_type,
              'path_to_resource' => $path_to_resource,
              'post_body' => $post_body,
              'comment_permission' => $comment_permission,
              'page_id' => $page_id,
              'author_id' =>$author_id,
              'parent_post' => $parent_post
          );
          $newPost = new Post($data);

          try {
              $post_id = $this->postDAO->createNewPost($newPost);
              if ($post_id > 0) {
                  $this->log->info('Created a new post', ['post_body' => $post_body]);
                  return array('success' => true, 'message' => ('New post on page ' . $page_id), 'post_id' => $post_id);
              }
          } catch (\PDOException $ex) {
              $this->log->error("A pdo exception occurred when creating a new post: " . $ex->getMessage());
          }
          return array(
              'success' => false,
              'message' => 'Something went wrong!'
          );
      }

    /**
     * @param $post Post entity to populate with the full author details.
     */
      private function populatePostAuthor($post) {
          if ($post) {
              try {
                  $author = $this->memberDAO->getMemberById($post->getAuthorId());
                  $post->setAuthor($author);
              } catch (\PDOException $ex) {
                  $this->log->error('A PDO exception prevented getting the author for post. '
                      . $ex->getMessage(), $post->toObject());
              }
          }
      }

    /**
     * @param $member Member
     * @param array $info ('memberPage': MemberPage or 'groupPage' : GroupPage, 'group': Group)
     * @return bool
     */
      private function hasFullAccess($member, $info = array()) {
          if ($member->isAdmin()) {
              return true;
          } elseif (isset($info['memberPage'])) {
              $memberPage = $info['memberPage'];
              if ($memberPage->getMemberId() == $member->getMemberId())
                  return true;

          } elseif (isset($info['groupPage'])) {
              $groupPage = $info['groupPage'];
              if ($groupPage->getPageOwner() == $member->getMemberId())
                  return true;
          } elseif (isset($info['group'])) {
              $group = $info['group'];
              if ($group->getGroupOwner() == $member->getMemberId())
                  return true;
          }
          return false;
      }

    /**
     * @param $member Member
     * @param $page_id int|string
     * @return [Post]
     */
    public function getPostsForMemberOnPage($member, $page_id, $additionalInfo)
    {
        $posts = $this->getPostsByPage($page_id);
        if ($this->hasFullAccess($member, $additionalInfo)) return $posts;

        // filter out the posts member cannot see.
        return array_filter($posts, function(Post $post) use ($member) {
            if ($post->getAuthorId() == $member->getMemberId()) return true;
            try {
                $permission = $this->postDAO->getPermissionForMemberOnPost($post->getPostId(), $member->getMemberId());
                return $permission !== Post::PERMISSION_DENIED;
            } catch (\PDOException $ex) {
                $this->log->error("Ouch! could not get permission on post ". $post->getPostId() .': '. $ex->getMessage());
                return false;
            }
        });
    }

    /**
     * Gets the posts on page id = -1 (admin exclusive page)
     * @return [Post]
     */
    public function getPublicPosts()
    {
        try {
            return $this->postDAO->getPostsByPage(-1);
        } catch (\PDOException $ex) {
            $this->log->error('There was an error while fetching public posts. ' . $ex->getMessage());
        }
        return [];
    }

    /**
     * @param $post Post The post entity.
     * @return array ['member' => Member, 'permission' => string]
     */
    public function getCustomAccessListForPost($post)
    {
        if ($post->getCommentPermission() !== Post::PERMISSION_TAILORED) {
            return []; // non custom list, just use whichever!
        }
        $res = $this->postDAO->getCustomAccessListOnPost($post->getPostId());
        return array_map(function ($it) {
            return [
                'member' => $this->memberDAO->getMemberById($it['member_id']),
                'permission' => $it['permission']
            ];
        }, $res);
    }

    /**
     * Adds a comment to a post.
     * @param $parent Post The parent post
     * @param $author Member The user who wrote this post.
     * @param $params array [Http POST request parameters to create a post]
     * @return array ['success' => bool, 'message' => string, 'post_id' => int]
     */
    public function addCommentToPost($parent, $author, $params)
    {
        // TODO: Implement addCommentToPost() method.
    }

    /**
     * Same as createNewPost except, receives raw request parameters and does validation
     * @param $author Member The post author
     * @param $params array [Http POST request parameters to create a post + added file in the array if any].
     * @param int|string $page_id
     * @return array ['success' => bool, 'message' => string]
     */
    public function createPost($author, $params, $page_id)
    {
        // TODO check if author can post on the given page id.
        if (!Validation::validateParametersExist([
           PostService::FIELD_BODY, PostService::FIELD_PERMISSION_TYPE
        ], $params))
        {
            return ['success' => false, 'message' => 'Permission type and text body is mandatory.'];
        }
        $post_body = $params[PostService::FIELD_BODY];
        $permission_type = $params[PostService::FIELD_PERMISSION_TYPE];
        $post_type = Post::TYPE_TEXT;
        $file = null;
        $parent_post = null;
        $path_to_resource = null;
        if (isset($params[PostService::FIELD_FILE])) {
            $post_type = Post::TYPE_IMAGE;
            /**
             * @var UploadedFile
             */
            $file = $params[PostService::FIELD_FILE];
            $res = Validation::validateImageOnly($file);
            if (!$res['success']) {
                $this->log->error('Image validation failed.', $res);
                return $res;
            }
        } elseif (isset($params[PostService::FIELD_PATH])) {
            $post_type = Post::TYPE_VIDEO;
            $path_to_resource = $params[PostService::FIELD_PATH];
        }

        if (isset($params[PostService::FIELD_PARENT])) {
            $parent_post = $params[PostService::FIELD_PARENT];
        }
        try {
            $post = new Post([
                'post_date_created' => date('YYYY-MM-DD'),
                'post_type' => $post_type,
                'path_to_resource' => $path_to_resource,
                'post_body' => $post_body,
                'comment_permission' => $permission_type,
                'page_id' => $page_id,
                'author_id' => $author->getMemberId(),
                'parent_post' => $parent_post
            ]);
            $id =  $this->postDAO->createNewPost($post);
            if ($id < 0) {
                return ['success' => false, 'message' => 'Could not create post!'];
            }
            if ($permission_type == Post::PERMISSION_TAILORED) {
                foreach ($params as $key => $value) {
                    if (is_numeric($key)) {
                        $this->postDAO->addCustomAccessForPost($key, $id, $value);
                    }
                }
            }
            if ($file) {
                // save file and update post.
                $target_dir = "assets/images/posts/$id/";
                $target_file = $target_dir . basename($file->getClientFilename());
                if (file_exists($target_file)) {
                    // delete file
                    unlink($target_file);
                }
                $res = Validation::validateImageUpload($target_file, $file);
                if ($res['success']) {
                    // save file and update post.
                    $file->moveTo($target_file);
                    $post->setPathToResource('/'.$target_file);
                    if ($this->postDAO->updatePost($post)) {
                        return ['success' => true, 'message' => 'Post created successfully!'];
                    } else {
                        return ['success' => false, 'message' => 'Post was partially created...'];
                    }
                } else {
                    return $res;
                }
            } else {
                return ['success' => true, 'message' => 'Post created successfully!'];
            }
        } catch (\PDOException $ex) {
            $this->log->error("A PDO exception prevented post from being created! " . $ex->getMessage());
        }
        return ['success' => false, 'message' => 'something went wrong!'];
    }

    /**
     * @param $post_id int|string The Post id.
     * @param $params [Http POST request parameters to update a post]
     * @return bool
     */
    public function updatePost($post_id, $params)
    {
        // TODO: Implement updatePost() method.
    }
}
