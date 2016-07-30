<?php

namespace Powon\Services;

use Powon\Entity\Post;

interface PostService {

  /**
  *@param $post_id int
  *@return post entity
  */
  public function getPostById($post_id);
  /**
  *@param $page_id int
  *@return post array|null
  */
  public function getPostsByPage($page_id);
  /**
  *@param $author_id int
  *@return post array|null
  */
  public function getPostsByAuthor($author_id);

    /**
     * @param $post_type string
     * @param $path_to_resource string
     * @param $post_body string
     * @param $comment_permission string
     * @param $page_id int
     * @param $author_id int
     * @return mixed array('success': bool, 'message':string)
     */
    public function createNewPost($post_type, $path_to_resource, $post_body, $comment_permission, $page_id, $author_id);
}
