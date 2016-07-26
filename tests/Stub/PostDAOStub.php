<?php
namespace Powon\Test\Stub;

use Powon\Dao\PostDAO;
use Powon\Entity\Post;

class PostDAOStub implements PostDAO {

  /**
   * @var array of mock post data.
   */
  public $posts;

  public function __construct()
  {
      $this->posts = [];
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
        if ($this->posts[$i]['page_id'] == $page_id) {
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
     * @return Post array|null
     */
  public function getPostsByAuthor($author_id){
  $postsByAuthor = array();
  for ($i = 0; $i < count($this->posts); $i++) {
      if ($this->posts[$i]['author_id'] == $author_id) {
          array_push($postsByAuthor, $this->posts[$i]);
        }
      }
      if(empty($postsByAuthor)){
        return null;
      }
      else{
      return array_map(function($data){
        return new Post($data);
      }, $postsByAuthor);
    }
  }
  /**
   * @param $entity Post
   * @return bool
   */
  public function createNewPost($entity)
  {
      $new_post = $entity->toObject();
      $this->posts[] = $new_post;
      return true;
  }
}

?>
