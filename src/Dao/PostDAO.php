<?php
namespace Powon\Dao;

use Powon\Entity\Post;

interface PostDAO {

  /**
   * @param int $post_id
   * @return Post|null
   */
public function getPostById($post_id);

/**
 * @param int $page_id
 * @return Post[] of post entities or null
 */
public function getPostsByPage($page_id);

/**
 * @param int $author_id
 * @return Post[] of post entities or null
 */
public function getPostsByAuthor($author_id);

/**
 * @param $entity Post
 * @return bool
 */
public function createNewPost($post);

}
