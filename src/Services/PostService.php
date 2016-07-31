<?php

namespace Powon\Services;

use Powon\Entity\Member;
use Powon\Entity\Post;

interface PostService {

    const FIELD_BODY = 'post_text';
    const FIELD_FILE = 'post_file';
    const FIELD_PATH = 'field_path';
    const FIELD_TYPE = 'field_type';
    const FIELD_REMOVE_FILE = 'remove_file';
    const FIELD_PERMISSION_TYPE = 'permission_type';

    const FIELD_PARENT = 'parent_post';

     /**
      * @param $post_id int
      * @return Post|null entity
      */
     public function getPostById($post_id);

     /**
      * @param $page_id int
      * @return [Post]
      */
     public function getPostsByPage($page_id);

     /**
      * @param $author_id int
      * @return [Post]
      */
     public function getPostsByAuthor($author_id);

    /**
     * @param $post_type string
     * @param $path_to_resource string
     * @param $post_body string
     * @param $comment_permission string
     * @param $page_id int
     * @param $author_id int
     * @param $parent_post int|string
     * @return array
     */
    public function createNewPost($post_type, $path_to_resource, $post_body,
                                  $comment_permission, $page_id, $author_id, $parent_post);

    /**
     * @param $member Member
     * @param $page_id int|string
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can see everything)
     * @return [Post]
     */
    public function getPostsForMemberOnPage($member, $page_id, $additionalInfo);

    /**
     * Gets the posts on page id = -1 (admin exclusive page)
     * @return [Post]
     */
    public function getPublicPosts();

    /**
     * @param $post Post The post entity.
     * @return array ['member' => Member, 'permission' => string]
     */
    public function getCustomAccessListForPost($post);

    /**
     * Same as createNewPost except, receives raw request parameters and does validation
     * @param $author Member The post author
     * @param $params array [Http POST request parameters to create a post].
     * @param $page_id int|string The page that contains this post.
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do everything)
     * @return array ['success' => bool, 'message' => string]
     */
    public function createPost($author, $params, $additionalInfo);

    /**
     * @param $post_id int|string The Post id.
     * @param $params [Http POST request parameters to update a post]
     * @param $requester Member who requested this update
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do anything)
     * @return array
     */
    public function updatePost($post_id, $params, $requester, $additionalInfo);

    /**
     * Deletes the given post entity
     * @param $post_id
     * @param $requester Member that requested this post to be deleted.
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do anything)
     * @return bool
     * @internal param int|string $post The post id
     */
    public function deletePost($post_id, $requester, $additionalInfo);

    /**
     * @param Member $member
     * @param Post $parent
     * @param $additionalInfo array ['memberPage' => MemberPage, 'groupPage' => GroupPage, 'Group' => Group]
     * The additional info is to determine whether to give full access to the posts (owners can do anything)
     * @return [Post]
     */
    public function getPostCommentsAccessibleToMember($member, Post $parent, $additionalInfo);

    /**
     * @param Member $member
     * @param Post $post
     * @param $additionalInfo
     * @return bool
     */
    public function canMemberEditPost($member, Post $post, $additionalInfo);

    /**
     * Returns the permission to the post as set by the post author.
     * @param Member $member
     * @param Post $post
     * @return string The permission
     */
    public function getCommentPermissionForMember(Member $member, Post $post);

}
