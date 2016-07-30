<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Post;
use Powon\Services\PostService;
use Powon\Dao\PostDAO;
use Psr\Log\LoggerInterface;
use Powon\Utils\DateTimeHelper;

class PostServiceImpl implements PostService
{
    /**
     * @var PostDAO
     */
    private $PostDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger, PostDAO $dao)
    {
        $this->PostDAO = $dao;
        $this->log = $logger;
    }

    /**
    *@param $post_id int
    *@return post entity
    */
    public function getPostById($post_id){
      try{
        return $this->PostDAO->getPostById($post_id);
      } catch (\PDOException $ex) {
        $this->log->error("A pdo exception occured: " . $ex->getMessage());
        return [];
      }
    }

    /**
    *@param $page_id int
    *@return post array|null
    */

    public function getPostsByPage($page_id){
      try {
          return $this->PostDAO->getPostsByPage($page_id);
      } catch (\PDOException $ex) {
          $this->log->error("A pdo exception occurred: " . $ex->getMessage());
          return [];
      }
    }

    /**
    *@param $author_id int
    *@return post array|null
    */
    public function getPostsByAuthor($author_id){
      try {
          return $this->PostDAO->getPostsByAuthor($author_id);
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
       * @return mixed array('success': bool, 'message':string)
       */
      public function createNewPost($post_type, $path_to_resource, $post_body, $comment_permission, $page_id, $author_id){
          //get current date when creating a new post
          $date = date("YYYY-MM-DD");
          $data = array(
              'post_date_created' => $date,
              'post_type' => $post_type,
              'path_to_resource' => $path_to_resource,
              'post_body' => $post_body,
              'comment_permission' => $comment_permission,
              'page_id' => $page_id,
              'author_id' =>$author_id
          );
          $newPost = new Post($data);

          try {
              if ($this->PostDAO->createNewPost($newPost)) {
                  $this->log->info('Created a new post', ['post_body' => $post_body]);
                  return array('success' => true, 'message' => ('New post on page ' . $page_id));
              }
          } catch (\PDOException $ex) {
              $this->log->error("A pdo exception occurred when creating a new post: " . $ex->getMessage());
          }
          return array(
              'success' => false,
              'message' => 'Something went wrong!'
          );
      }
}
