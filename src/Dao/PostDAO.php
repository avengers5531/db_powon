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
     * @param $post Post
     * @return bool
     */
    public function createNewPost($post);

    /**
     * Updates post body, resource_path, comment_permission
     * @param $post Post
     * @return bool
     */
    public function updatePost($post);

    /**
     * @param $post_id int|string the post id.
     * @return bool
     */
    public function deleteCustomAccessesForPost($post_id);

    /**
     * @param $member_id int|string the member id
     * @param $post_id int|string the post id
     * @param $permission string The permission character.
     * @return bool
     */
    public function addCustomAccessForPost($member_id, $post_id, $permission);

    /**
     * @param $post_id int|string
     * @return array ['member_id' => int, 'permission' => string]
     */
    public function getCustomAccessListOnPost($post_id);

    /**
     * @param $post_id
     * @param $member_id
     * @return string (see Post for the different PERMISSION constants)
     */
    public function getPermissionForMemberOnPost($post_id, $member_id);

}
