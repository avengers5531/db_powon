<?php

namespace Powon\Services\Implementation;

use Powon\Dao\MemberDAO;
use Powon\Entity\Group;
use Powon\Entity\GroupPage;
use Powon\Entity\Member;
use Powon\Entity\MemberPage;
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
          $data = array(
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
          if (!$member)
              return false;
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
            $posts = $this->postDAO->getPostsByPage(-1);
            foreach ($posts as &$post) {
                $this->populatePostAuthor($post);
            }
            return $posts;
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
     * Returns the permission to the post as set by the post author.
     * @param Member $member
     * @param Post $post
     * @param $additional_info array
     * @return string The permission
     */
    public function getCommentPermissionForMember(Member $member, Post $post, $additional_info) {
        if ($member->getMemberId() == $post->getAuthorId() || $this->hasFullAccess($member, $additional_info)) {
            return Post::PERMISSION_ADD_CONTENT;
        }
        $post_permission = $post->getCommentPermission();
        if ($post_permission !== Post::PERMISSION_TAILORED) {
            return $post_permission;
        }
        try {
            return $this->postDAO->getPermissionForMemberOnPost($post->getPostId(), $member->getMemberId());
        } catch (\PDOException $ex) {
           $this->log->error("Couldn't retrieve comment permission on post for member:" ,
               ['post' => $post->getPostId(), 'member' => $member->getMemberId()]);
        }
        return Post::PERMISSION_DENIED;
    }

    /**
     * Same as createNewPost except, receives raw request parameters and does validation
     * @param $author Member The post author
     * @param $params array [Http POST request parameters to create a post].
     * @param $page_id int|string The page that contains this post.
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do everything)
     * @return array ['success' => bool, 'message' => string]
     */
    public function createPost($author, $params, $additionalInfo)
    {
        // The code here looks terrible! So procedural... Sorry about that ^^;
        $this->log->debug('Create post with params:', $params);
        $page_id = null;
        $has_full_access = $this->hasFullAccess($author, $additionalInfo);
        if (isset($additionalInfo['memberPage'])) {
            /**
             * @var $memberPage MemberPage
             */
            $memberPage = $additionalInfo['memberPage'];
            $page_id = $memberPage->getPageId();
            if (!$has_full_access) {
                // TODO check access.
            }
        } elseif (isset($additionalInfo['groupPage']) && isset($additionalInfo['group'])) {
            /**
             * @var GroupPage $groupPage
             */
            $groupPage = $additionalInfo['groupPage'];
            $page_id = $groupPage->getPageId();

            if (!$has_full_access) {
                // TODO check access and return if no access.
                // use GroupPageService
            }
        } else {
            $this->log->error("PostServiceImpl::createPost: Additional info is missing. ".
            "This must be a programming error from the caller of the PostService.");
            return ['success' => false, 'message' => 'Missing additional information'];
        }
        if (!Validation::validateParametersExist([
            PostService::FIELD_BODY, PostService::FIELD_PERMISSION_TYPE, PostService::FIELD_TYPE
        ], $params))
        {
            return ['success' => false, 'message' => 'Permission type, post type and text body is mandatory.'];
        }
        $post_body = $params[PostService::FIELD_BODY];
        $permission_type = $params[PostService::FIELD_PERMISSION_TYPE];
        $post_type = $params[PostService::FIELD_TYPE];
        $can_add_content = true;
        $file = null;
        $parent_post_id = null;
        $path_to_resource = null;

        // check if we're creating a comment. If so, check the permissions also.
        if (isset($params[PostService::FIELD_PARENT])) {
            $parent_post_id = $params[PostService::FIELD_PARENT];
            try {
                $parent_post = $this->postDAO->getPostById($parent_post_id);
                if (!$parent_post) {
                    $this->log->error("Failed to get parent post with id $parent_post_id.");
                    return ['success' => false, 'message' => 'Parent post does not exist...'];
                }
                $comment_permission = $this->getCommentPermissionForMember($author, $parent_post, $additionalInfo);
                if ($comment_permission === Post::PERMISSION_VIEW_ONLY || $comment_permission === Post::PERMISSION_DENIED) {
                    $this->log->warning("User tried to comment on post with permission $comment_permission", $author->toObject());
                    return ['success' => false, 'message' => 'You cannot comment on this post.'];
                } elseif ($comment_permission === Post::PERMISSION_COMMENT) {
                    $can_add_content = false;
                }
            } catch (\PDOException $ex) {
                $this->log->error("PostServiceImpl::createPost: PDO exception when getting parent post $parent_post_id ". $ex->getMessage());
                return ['success' => false, 'message' => 'Something went wrong!'];
            }
        }
        if (!$can_add_content && ( $post_type === Post::TYPE_IMAGE
                || $post_type === Post::TYPE_VIDEO))
        {
            return ['success' => false, 'message' => 'You cannot add content to this post.'];
        }
        if ($post_type === Post::TYPE_IMAGE) {
            if (!isset($params[PostService::FIELD_FILE])) {
                return ['success' => false, 'message' => 'Image was not provided'];
            }
            /**
             * @var UploadedFile
             */
            $file = $params[PostService::FIELD_FILE];
            $res = Validation::validateImageOnly($file);
            if (!$res['success']) {
                $this->log->error('Image validation failed.', $res);
                return $res;
            }
        } elseif ($post_type === Post::TYPE_VIDEO) {
            if (!isset($params[PostService::FIELD_PATH])) {
               return ['success' => false, 'message' => 'Video code was not provided.'];
            }
            $video_id = $params[PostService::FIELD_PATH];
            if (preg_match('/[a-zA-Z0-9_-]{11}/', $video_id) !== 1) {
                return ['success' => false, 'message' => 'YouTube video code is invalid.'];
            }
            $path_to_resource = $video_id;
        }

        try {
            $post = new Post([
                'post_type' => $post_type,
                'path_to_resource' => $path_to_resource,
                'post_body' => $post_body,
                'comment_permission' => $permission_type,
                'page_id' => $page_id,
                'author_id' => $author->getMemberId(),
                'parent_post' => $parent_post_id
            ]);
            $id =  $this->postDAO->createNewPost($post);
            if ($id < 0) {
                return ['success' => false, 'message' => 'Could not create post!'];
            }
            $post->setPostId($id);
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
                } elseif (!file_exists($target_dir)) {
                    mkdir($target_dir, 0775, true);
                }
                // save file and update post.
                $file->moveTo($target_file);
                $post->setPathToResource('/'.$target_file);
                if ($this->postDAO->updatePost($post)) {
                    return ['success' => true, 'message' => 'Post created successfully!'];
                } else {
                    return ['success' => false, 'message' => 'Post was partially created...'];
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
     * @param $requester Member who requested this update
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do anything)
     * @return array
     */
    public function updatePost($post_id, $params, $requester, $additionalInfo)
    {
        $this->log->debug('Update post with params:' ,$params);
        if (!isset($additionalInfo['memberPage']) && (!isset($additionalInfo['group']) || !isset($additionalInfo['groupPage'])))
        {
            $this->log->error("PostServiceImpl::updatePost - Additional info is missing. ".
                "This must be a programming error from the caller of the PostService.");
            return ['success' => false, 'message' => 'Missing additional information'];
        }
        try {
            $post = $this->postDAO->getPostById($post_id);
            if (!$post) {
                $this->log->error('Post does not exist.');
                return ['success' => false, 'message' => 'Post does not exist.'];
            }
            if ( !($this->hasFullAccess($requester, $additionalInfo) ||
                 $post->getAuthorId() == $requester->getMemberId() ) )
            {
                // cannot edit post
                $this->log->error("Member cannot edit post $post_id.", $requester->getMemberId());
                return ['success' => false, 'message' => 'You cannot edit this post.'];
            }
            if (!Validation::validateParametersExist([
                PostService::FIELD_BODY, PostService::FIELD_PERMISSION_TYPE, PostService::FIELD_TYPE
            ], $params))
            {
                return ['success' => false, 'message' => 'Permission type and text body is mandatory.'];
            }
            // check if we're updating a comment. If so, check the permissions also.
            $parent_post_id = $post->getParentPostId();
            $can_add_content = true;
            if ($parent_post_id) {
                $parent_post_id = $params[PostService::FIELD_PARENT];
                try {
                    $parent_post = $this->postDAO->getPostById($parent_post_id);
                    if (!$parent_post) {
                        $this->log->error("Failed to get parent post with id $parent_post_id.");
                        return ['success' => false, 'message' => 'Parent post does not exist...'];
                    }
                    $comment_permission = $this->getCommentPermissionForMember($requester, $parent_post, $additionalInfo);
                    if ($comment_permission === Post::PERMISSION_VIEW_ONLY || $comment_permission === Post::PERMISSION_DENIED) {
                        $this->log->warning("User tried to edit a comment on post with permission $comment_permission", $requester->toObject());
                        return ['success' => false, 'message' => 'You cannot have a comment on this post.'];
                    } elseif ($comment_permission === Post::PERMISSION_COMMENT) {
                        $can_add_content = false;
                    }
                } catch (\PDOException $ex) {
                    $this->log->error("PostServiceImpl::createPost: PDO exception when getting parent post $parent_post_id ". $ex->getMessage());
                    return ['success' => false, 'message' => 'Something went wrong!'];
                }
            }
            // update post body
            $post->setPostBody($params[PostService::FIELD_BODY]);

            // update type
            $old_type = $post->getPostType();
            $new_type = $params[PostService::FIELD_TYPE];
            if (!$can_add_content && $new_type !== Post::TYPE_TEXT) {
                return ['success' => false, 'message' => 'You are not allowed to add content to this post.'];
            }
            if (($old_type === Post::TYPE_IMAGE  && isset($params[PostService::FIELD_REMOVE_FILE])
                || isset($params[PostService::FIELD_FILE])))
            {
                // delete image (remove the '/' from the beginning...)
                $path = substr($post->getPathToResource(), 1);
                if (file_exists($path)) {
                    unlink($path);
                }
                $post->setPathToResource(null);
            }
            $post->setPostType($new_type);

            // update path if it's video or image
            if ($new_type === Post::TYPE_VIDEO) {
                if (!isset($params[PostService::FIELD_PATH])) {
                    return ['success' => false, 'message' => 'No video code provided.'];
                }
                $video_id = $params[PostService::FIELD_PATH];
                if (preg_match('/[a-zA-Z0-9_-]{11}/', $video_id) !== 1) {
                    return ['success' => false, 'message' => 'YouTube video code is invalid.'];
                }
                $post->setPathToResource($video_id);
            } elseif ($new_type === Post::TYPE_IMAGE) {
                if ((!isset($params[PostService::FIELD_FILE]) && !$post->getPathToResource())) {
                    // new type image but no image is set.
                    return ['success' => false, 'message' => 'No image file found!'];
                }
                if (isset($params[PostService::FIELD_FILE])) {
                    // update image
                    /**
                     * @var $file UploadedFile
                     */
                    $file = $params[PostService::FIELD_FILE];
                    $target_dir = "assets/images/posts/$post_id/";
                    $target_file = $target_dir . basename($file->getClientFilename());
                    $res = Validation::validateImageUpload($target_file, $file);
                    if (!$res['success']) {
                        return $res;
                    }
                    if (file_exists($target_file)) {
                        // delete file
                        unlink($target_file);
                    } elseif (!file_exists($target_dir)) {
                        mkdir($target_dir, 0775, true);
                    }
                    // save file and update post.
                    $file->moveTo($target_file);
                    $post->setPathToResource('/' . $target_file);
                }
            }

            // permission and update post access list
            $old_permission = $post->getCommentPermission();
            $new_permission = $params[PostService::FIELD_PERMISSION_TYPE];
            if ($old_permission === Post::PERMISSION_TAILORED) {
                $this->postDAO->deleteCustomAccessesForPost($post_id);
            }
            $post->setCommentPermission($new_permission);
            if ($new_permission === Post::PERMISSION_TAILORED) {
                foreach ($params as $id => $perm) {
                    if (is_numeric($id)) {
                        $this->postDAO->addCustomAccessForPost($id, $post_id, $perm);
                    }
                }
            }

            // save in db
            $this->postDAO->updatePost($post);

        } catch (\PDOException $ex) {
            $this->log->error("PostServiceImpl::updatePost - pdo exception when updating post $post_id. ". $ex->getMessage());
        }

        return ['success' => true, 'message' => 'Post updated successfully!'];
    }

    /**
     * Deletes the given post entity
     * @param $post_id
     * @param $requester Member that requested this post to be deleted.
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do anything)
     * @return array
     * @internal param int|string $post The post id
     */
    public function deletePost($post_id, $requester, $additionalInfo)
    {
        if (!isset($additionalInfo['memberPage']) && (!isset($additionalInfo['group']) || !isset($additionalInfo['groupPage'])))
        {
            $this->log->error("PostServiceImpl::deletePost - Additional info is missing. ".
                "This must be a programming error from the caller of the PostService.");
            return ['success' => false, 'message' => 'Missing additional information'];
        }
        try {
            $post = $this->postDAO->getPostById($post_id);
            if (!$post) {
                return ['success' => false, 'message' => 'Post does not exist.'];
            }
            // can delete post if requester is author of the post or if requester if owner of parent post.
            $can_delete = $post->getAuthorId() == $requester->getMemberId() || $this->hasFullAccess($requester, $additionalInfo);
            if (!$can_delete) {
                // check if he owns the parent post.
                $parent_id = $post->getParentPostId();
                if ($parent_id) {
                    $parent = $this->postDAO->getPostById($parent_id);
                    $can_delete = $parent->getAuthorId() == $requester->getMemberId();
                }
            }
            if ($can_delete) {
                if ($post->getPostType() === Post::TYPE_IMAGE) {
                    // delete file
                    // remove the '/' at the beginning
                    $file_path = substr($post->getPathToResource(), 1);
                    unlink($file_path);
                    // could delete the folder too (it should be empty at this point)
                    rmdir("assets/images/posts/$post_id");
                }
                if ($this->postDAO->deletePost($post->getPostId())) {
                    return ['success' => true, 'message' => 'Post was deleted.'];
                }
            } else {
                return ['success' => false, 'message' => 'You are not allowed to delete this post.'];
            }
        } catch (\Exception $ex) {
            $this->log->error("PostServiceImpl::deletePost - Exception was thrown while trying to delete post: " . $ex->getMessage());
        }
        return ['success' => false, 'message' => 'Something went wrong!'];
    }


    /**
     * @param Member $member
     * @param Post $parent
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do anything)
     * @return [Post]
     */
    public function getPostCommentsAccessibleToMember($member, Post $parent, $additionalInfo)
    {
        if (!$member) {
            return [];
        }
        if (!isset($additionalInfo['memberPage']) && (!isset($additionalInfo['group']) || !isset($additionalInfo['groupPage'])))
        {
            $this->log->error("PostServiceImpl::getPostCommentsAccessibleToMember - Additional info is missing. ".
                "This must be a programming error from the caller of the PostService.");
            return [];
        }
        try {
            $posts = $this->postDAO->getChildrenPosts($parent->getPostId());
            foreach ($posts as &$comment) {
                $this->populatePostAuthor($comment);
            }
            // author of parent post can see all posts.
            if ($this->hasFullAccess($member, $additionalInfo) || $parent->getAuthorId() == $member->getMemberId()) {
                return $posts;
            }
            return array_filter($posts, function(Post $post) use ($parent, $member) {
                // member is the owner
                if ($post->getAuthorId() == $member->getMemberId()) return true;
                try {
                    $permission = $this->postDAO->getPermissionForMemberOnPost($post->getPostId(), $member->getMemberId());
                    return $permission !== Post::PERMISSION_DENIED;
                } catch (\PDOException $ex) {
                    $this->log->error("Ouch! could not get permission on post ". $post->getPostId() .': '. $ex->getMessage());
                    return false;
                }
            });
        } catch (\PDOException $ex) {
            $this->log->error("PostServiceImpl::getPostCommentsAccessibleToMember, ".
                "could not get comments on post " . $parent->getPostId());
        }
        return [];
    }

    /**
     * @param Member $member
     * @param Post $post
     * @param $additionalInfo
     * @return bool
     */
    public function canMemberEditPost($member, Post $post, $additionalInfo)
    {
        if ($member)
            return $this->hasFullAccess($member, $additionalInfo) || $post->getAuthorId() == $member->getMemberId();
        return false;
    }
}
