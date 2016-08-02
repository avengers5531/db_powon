<?php
namespace Powon\Test\Stub;

use Powon\Dao\PostDAO;
use Powon\Entity\Post;

class PostDAOStub implements PostDAO {

  /**
   * @var array of mock post data.
   */
  public $posts;
    public $custom_access;

  public function __construct()
  {
      $this->posts = [];
      $this->custom_access = [];
  }
  /**
   * @param int $post_id
   * @return Post|null
   */
  public function getPostById($post_id)
  {
      for ($i = 0; $i < count($this->posts); $i++) {
          if ($this->posts[$i]['post_id'] == $post_id) {
              return new Post($this->posts[$i]);
          }
      }
      return null;
  }
  /**
   * @param int $page_id
   * @return Post array|null
   */
  public function getPostsByPage($page_id){
    $postsByPage = array();
    for ($i = 0; $i < count($this->posts); $i++) {
        if ($this->posts[$i]['page_id'] == $page_id && $this->posts[$i]['parent_post'] == null) {
            array_push($postsByPage, $this->posts[$i]);
          }
        }
        if(empty($postsByPage)){
          return null;
        }
        else {
        return array_map(function($data){
          return new Post($data);
        }, $postsByPage);
      }
    }

    /**
     * @param int $author_id
     * @return Post array
     */
  public function getPostsByAuthor($author_id){
  $postsByAuthor = array();
  for ($i = 0; $i < count($this->posts); $i++) {
      if ($this->posts[$i]['author_id'] == $author_id && $this->posts[$i]['parent_post'] == null) {
          array_push($postsByAuthor, $this->posts[$i]);
        }
      }
      return array_map(function($data){
        return new Post($data);
      }, $postsByAuthor);
  }
  /**
   * @param $entity Post
   * @return int
   */
  public function createNewPost($entity)
  {
      $new_post = $entity->toObject();
      $new_post['post_id'] = rand(22, 44);
      $this->posts[] = $new_post;
      return $new_post['post_id'];
  }

    /**
     * @param $post_id int|string
     * @return ['member_id' => int, 'permission' => string]
     */
    public function getCustomAccessListOnPost($post_id)
    {
        $res = [];
        foreach ($this->custom_access as &$access) {
            if ($access['post_id'] == $post_id) {
                $res[] = [
                    'member_id' => $access['member_id'],
                    'permission' => $access['comment_permission']
                ];
            }
        }
        return $res;
    }

    /**
     * Updates post body, resource_path, comment_permission
     * @param $post Post
     * @return bool
     */
    public function updatePost($post)
    {
        for ($i = 0; $i < count($this->posts); ++$i) {
            if ($this->posts[$i]['post_id'] == $post->getPostId()) {
                $item  = $post->toObject();
                unset($item['author']);
                $this->posts[$i] = $item;
                return true;
            }
        }
        return false;
    }

    /**
     * @param $post_id int|string the post id.
     * @return bool
     */
    public function deleteCustomAccessesForPost($post_id)
    {
        $count = count($this->custom_access);
        $this->custom_access = array_filter($this->custom_access, function ($it)
        use ($post_id)
        {
            return ($it['post_id'] != $post_id);
        });
        return $count != count($this->custom_access);
    }

    /**
     * @param $member_id int|string the member id
     * @param $post_id int|string the post id
     * @param $permission string The permission character.
     * @return bool
     */
    public function addCustomAccessForPost($member_id, $post_id, $permission)
    {
        $cc = [
            'post_id' => $post_id,
            'member_id' => $member_id,
            'comment_permission' => $permission
        ];
        $this->custom_access[] = $cc;
    }

    /**
     * @param $post_id
     * @param $member_id
     * @return string (see Post for the different PERMISSION constants)
     */
    public function getPermissionForMemberOnPost($post_id, $member_id)
    {
        foreach ($this->posts as &$post) {
            if ($post['post_id'] == $post_id) {
                if ($post['comment_permission'] != Post::PERMISSION_TAILORED) {
                    return $post['comment_permission'];
                }
                break;
            }
        }
        foreach ($this->custom_access as &$custom) {
            if ($custom['member_id'] == $member_id && $custom['post_id'] == $post_id) {
                return $custom['comment_permission'];
            }
        }
        return Post::PERMISSION_DENIED;
    }

    /**
     * @param $parent int|string The parent post id
     * @return [Post]
     */
    public function getChildrenPosts($parent)
    {
        $children = [];
        foreach ($this->posts as &$post) {
           if ($post['parent_post'] == $parent) {
               $children[] = $post;
           }
        }
        return array_map(function($it) {
           return new Post($it);
        }, $children);
    }

    /**
     * @param $post_id string|int
     * @return bool
     */
    public function deletePost($post_id)
    {
        $count = count($this->posts);
        $this->posts = array_filter($this->posts, function($it)
        use ($post_id)
        {
            return $it['post_id'] != $post_id;
        });
        return count($this->posts) != $count;
    }
}

