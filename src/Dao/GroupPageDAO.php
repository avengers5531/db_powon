<?php

namespace Powon\Dao;

use Powon\Entity\GroupPage;
use Powon\Entity\Member;

interface GroupPageDAO {

    /**
     * @param $page_id
     * @return GroupPage|null
     */
    public function getGroupPageById($page_id);

    /**
     * @param $page_title
     * @return GroupPage|null
     */
    public function getGroupPageByTitle($page_title);

    /**
     * @param $group_id
     * @return [GroupPage]
     */
    public function getPagesOfGroup($group_id);

    /**
     * @param $group_page
     * @return int The id of the newly created group page
     */
    public function createGroupPage($group_page);

    /**
     * @param $page_id
     * @return bool
     */
    public function deleteGroupPage($page_id);

    /**
     * @param $page_id
     * @param $input
     * @return bool
     */
    public function updateGroupPageTitle($page_id, $input);

    /**
     * @param $page_id
     * @param $input
     * @return bool
     */
    public function updateGroupPageDescription($page_id, $input);

    /**
     * @param $member_id
     * @param $group_id
     * @return null|\Powon\Entity\GroupPage[]
     */
    public function getGroupPagesForMember($member_id, $group_id);

    /**
     * @param $page_id
     * @param $access_type
     * @return mixed
     */
    public function updateAccessType($page_id, $access_type);

    /**
     * @param $page_id
     * @return Member[]|null
     */
    public function getMembersWithPageAccess($page_id);

    /**
     * @param $page_id
     * @param $member_id
     * @return bool
     */
    public function deleteGroupPageMembers($page_id, $member_id);

    /**
     * @param $page_id
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function addMemberToGroupPage($page_id, $member_id, $group_id);
}
