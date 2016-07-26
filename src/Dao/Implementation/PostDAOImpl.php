<?php

namespace Powon\Dao\Implementation;

use Powon\Dao\PostDAO as PostDAO;
use Powon\Entity\Post as Post;

//parent post not implemented - going to change schema?

class PostDAOImpl implements PostDAO {

    private $db;
    /**
     * MemberDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @param int $post_id
     * @return Post|null
     */
    public function getPostById($id)
    {
        $sql = 'SELECT p.post_date_created,
                p.post_type,
                p.path_to_resource,
                p.post_body,
                p.comment_permission,
                p.page_id,
                p.author_id
                FROM post p
                WHERE post_id = :id';

                /*
                *check this - copied from member Dao
                */
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Post($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param int $page_id
     * @return Post[] of post entities.
     */
    public function getPostsByPage($page_id){

          $sql = 'SELECT p.post_date_created,
                  p.post_type,
                  p.path_to_resource,
                  p.post_body,
                  p.comment_permission,
                  p.page_id,
                  p.author_id
                  FROM post p
                  WHERE page_id = :page_id';

                  $stmt = $this->db->prepare($sql);
                  $stmt->bindParam(':page_id', page_id, \PDO::PARAM_INT);
                  $stmt->execute();
                  $results = $stmt->fetchAll();
                  if(empty($results)){
                    return null;
                  }
                  else
                  {return array_map(function ($row) {
                      return new Post($row);
                  },$results);
                }
    }
    /**
     * @param int $author_id
     * @return Post[] of post entities or null if empty
     */
    public function getPostsByAuthor($author_id){
      $sql = 'SELECT p.post_date_created,
              p.post_type,
              p.path_to_resource,
              p.post_body,
              p.comment_permission,
              p.page_id,
              p.author_id
              FROM post p
              WHERE author_id = :author_id';

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':author_id', $author_id, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if(empty($results)){
          return null;
        }
        else{
        return array_map(function ($row) {
            return new Post($row);
        },$results);
      }
    }
    /**
     * @param $entity Post
     * @return bool
     */
    public function createNewPost($post)
    {
      $sql = 'INSERT INTO post(post_date_created, post_type, path_to_resource,
              post_body, comment_permission, page_id, author_id)
              VALUES
              (:post_date_created, :post_type, :path_to_resource, :post_body, :comment_permission, :page_id, :author_id)';
              $stmt = $this->db->prepare($sql);
              $stmt-> bindValue(':post_date_created', $post->getPostDateCreated(), \PDO::PARAM_STR);
              $stmt-> bindValue(':post_type', $post->getPostType(), \PDO::PARAM_STR);
              $stmt-> bindValue(':path_to_resource', $post->getPathToResource(), \PDO::PARAM_STR);
              $stmt-> bindValue(':post_body', $post->getPostBody(), \PDO::PARAM_STR);
              $stmt-> bindValue(':comment_permission', $post->getCommentPermission(), \PDO::PARAM_STR);
              $stmt-> bindValue(':page_id', $post->getPageId(), \PDO::PARAM_INT);
              $stmt-> bindValue(':author_id', $post->getAuthorId(), \PDO::PARAM_INT);
              return $stmt->execute();





    }
  }
?>
