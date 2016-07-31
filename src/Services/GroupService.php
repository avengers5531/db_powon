<?php

namespace Powon\Services;

use Powon\Entity\Group;
use Powon\Entity\Member;

interface GroupService {

    const GROUP_TITLE = 'group_title';
    const GROUP_DESCRIPTION = 'description';
    const GROUP_MEMBER_ID = 'member_id';
    
    //existing member details for validation
    const GROUP_PAGE_TITLE = 'group_page_title';

    /**
     * @param $id int The group's id
     * @return Group|null
     */
    public function getGroupById($group_id);

    /**
     * @param $member_id int the member's id
     * @param $group_id int the group id.
     * @return bool True if member belongs to group, false otherwise
     */
    public function memberBelongsToGroup($member_id, $group_id);

    /**
     * Retrieves all the groups a member belongs to
     * @param $member_id int The member's group
     * @return [Group] array of Group entities
     */
    public function getGroupsMemberBelongsTo($member_id);

    /**
     * All groups minus the ones member belongs to
     * @param $member_id int The member's group
     * @return [Group] array of Group entities
     */
    public function getGroupsMemberDoesNotBelongTo($member_id);

    /**
     * Validates the request parameters and creates a group with that owner.
     * @param $member_id int the owner id
     * @param $paramsRequest array The http request body.
     * It should contain self::GROUP_TITLE and self::GROUP_DESCRIPTION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function createGroupOwnedBy($member_id, $paramsRequest);

    /**
     * Validates the request parameters and updates the group with the given id.
     * @param $group_id int The group to update
     * @param $paramsRequest array The http request body.
     * It should contain self::GROUP_TITLE and self::GROUP_DESCRIPTION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateGroup($group_id, $paramsRequest);

    /**
     * Deletes the group with given group id
     * @param $group_id
     * @return bool true on success, false on failure
     */
    public function deleteGroup($group_id);

    /**
     * Creates a request in the database
     * @param $requestor_id int The id from the member who submitted the request.
     * @param $group_id int The group id.
     * @return bool true on success, false otherwise
     */
    public function createRequestToJoinGroup($requestor_id, $group_id);

    /**
     * Updates the request with an approval date. This should be called by a group owner.
     * @param $requestor_id
     * @param $group_id
     * @return bool true on success, false otherwise.
     */
    public function acceptRequestToJoinGroup($requestor_id, $group_id);

    /**
     * Returns an array of member entities who have submitted a request to join the group
     * but haven't received approval.
     * @param $group_id int The group id.
     * @return [Member]
     */
    public function getMembersWithPendingRequestsToGroup($group_id);

    /**
     * Removes a member from a group.
     * @param $member_id int The member's id to remove
     * @param $group_id int The group id
     * @return bool true on success, false otherwise
     */
    public function removeMemberFromGroup($member_id, $group_id);

    /**
     * Gets all the members belonging to a group.
     * @param $group_id int The group id.
     * @return [Member]
     */
    public function getGroupMembers($group_id);

    /**
     * Gets a list of Group entities that contain the search term in the title or the description.
     * @param $searchTerm string The search term
     * @return [Group]
     */
    public function searchGroups($searchTerm);

    /**
     * Checks whether a member is waiting for approval in a certain group
     * @param $member_id int the member id
     * @param $group_id int The group id
     * @return bool True if member is indeed waiting for approval
     */
    public function memberWaitingForApproval($member_id, $group_id);

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function addNewMember($member_id, $group_id);

    // TODO later: setGroupPicture
}
