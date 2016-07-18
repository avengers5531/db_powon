<?php

namespace Powon\Services;


use Powon\Entity\GroupPage;

interface GroupPageService
{
    const FIELD_PAGE_TITLE = 'page_title';
    const FIELD_PAGE_DESCRIPTION = 'page_description';
    const FIELD_ACCESS = 'access_type';

    /**
     * Creates a page within the given group. Also inserts the owner id in the member_has_access table
     * for this page.
     * @param $page_owner int the member_id of the page owner
     * @param $group_id int the group id
     * @param $requestParams array: Contains the post request parameters.
     * @return array
     * Returns an array of the following form:
     * ['success' => bool, 'message' => string, 'page_id' => int (The id of the newly created page if successful)]
     */
    public function createGroupPage($page_owner, $group_id, $requestParams);

    /**
     * Updates the title and description of the page. Changing owner is not supported.
     * @param $page_id int
     * @param $requestParams array
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateGroupPage($page_id, $requestParams);

    /**
     * @param $page_id int The id of the page to delete
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteGroupPage($page_id);

    /**
     * Gets a GroupPage entity by page id.
     * @param $page_id int
     * @return GroupPage|null
     */
    public function getPageById($page_id);

    /**
     * Returns all the pages for the group.
     * This method should only be called by the group owner or admin.
     * @param $group_id int
     * @return [GroupPage]
     */
    public function getGroupPages($group_id);

    /**
     * Returns the group pages for the given member (i.e. makes sure member has access to the pages)
     * @param $group_id int
     * @param $member_id int
     * @return  [GroupPage]
     */
    public function getGroupPagesForMember($group_id, $member_id);

    /**
     * This method updates the page access (access_type). It deletes all the users associated with the given page id
     * from the member_can_access_page table EXCEPT the owner.
     * Then, IF the access is private, it adds the new members given in the array to the table. Otherwise
     * it ignores the 3rd parameter after setting the access_type to public.
     * @param $page_id int
     * @param $access_type string (either GroupPage::ACCESS_EVERYONE or GroupPage::ACCESS_PRIVATE)
     * @param $requestParams array
     * This array contains the list of member_id as keys. i.e,
     * iterates through the keys of the array to get all the member ids to add to the member_can_access_page table if
     * access_type is private.
     * example: foreach($requestParams as $member_id => $value) {
     *     //do stuff with $member_id and ignore $value
     * }
     * @return array ['success' => bool, 'message' => string]
     */
    public function updatePageAccess($page_id, $access_type, $requestParams);
}
