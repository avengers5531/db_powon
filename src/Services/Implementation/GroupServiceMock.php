<?php

namespace Powon\Services\Implementation;


use Powon\Services\GroupService;

class GroupServiceMock implements GroupService
{

    /**
     * @param $member_id int the member's id
     * @param $group_id int the group id.
     * @return bool True if member belongs to group, false otherwise
     */
    public function memberBelongsToGroup($member_id, $group_id)
    {
        // TODO: Implement memberBelongsToGroup() method.
    }

    /**
     * Retrieves all the groups a member belongs to
     * @param $member_id int The member's group
     * @return [Group] array of Group entities
     */
    public function getGroupsMemberBelongsTo($member_id)
    {
        // TODO: Implement getGroupsMemberBelongsTo() method.
    }

    /**
     * All groups minus the ones member belongs to
     * @param $member_id int The member's group
     * @return [Group] array of Group entities
     */
    public function getGroupsMemberDoesNotBelongTo($member_id)
    {
        // TODO: Implement getGroupsMemberDoesNotBelongTo() method.
    }

    /**
     * Validates the request parameters and creates a group with that owner.
     * @param $member_id int the owner id
     * @param $paramsRequest array The http request body.
     * It should contain self::GROUP_TITLE and self::GROUP_DESCRIPTION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function createGroupOwnedBy($member_id, $paramsRequest)
    {
        // TODO: Implement createGroupOwnedBy() method.
    }

    /**
     * Validates the request parameters and updates the group with the given id.
     * @param $group_id int The group to update
     * @param $paramsRequest array The http request body.
     * It should contain self::GROUP_TITLE and self::GROUP_DESCRIPTION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateGroup($group_id, $paramsRequest)
    {
        // TODO: Implement updateGroup() method.
    }

    /**
     * Deletes the group with given group id
     * @param $group_id
     * @return bool true on success, false on failure
     */
    public function deleteGroup($group_id)
    {
        // TODO: Implement deleteGroup() method.
    }

    /**
     * Creates a request in the database
     * @param $requestor_id int The id from the member who submitted the request.
     * @param $group_id int The group id.
     * @return bool true on success, false otherwise
     */
    public function createRequestToJoinGroup($requestor_id, $group_id)
    {
        // TODO: Implement createRequestToJoinGroup() method.
    }

    /**
     * Updates the request with an approval date. This should be called by a group owner.
     * @param $requestor_id
     * @param $group_id
     * @return bool true on success, false otherwise.
     */
    public function acceptRequestToJoinGroup($requestor_id, $group_id)
    {
        // TODO: Implement acceptRequestToJoinGroup() method.
    }

    /**
     * Returns an array of member entities who have submitted a request to join the group
     * but haven't received approval.
     * @param $group_id int The group id.
     * @return [Member]
     */
    public function getMembersWithPendingRequestsToGroup($group_id)
    {
        // TODO: Implement getMembersWithPendingRequestsToGroup() method.
    }

    /**
     * Removes a member from a group.
     * @param $member_id int The member's id to remove
     * @param $group_id int The group id
     * @return bool true on success, false otherwise
     */
    public function removeMemberFromGroup($member_id, $group_id)
    {
        // TODO: Implement removeMemberFromGroup() method.
    }

    /**
     * Gets all the members belonging to a group.
     * @param $group_id int The group id.
     * @return [Member]
     */
    public function getGroupMembers($group_id)
    {
        // TODO: Implement getGroupMembers() method.
    }

    /**
     * Gets a list of Group entities that contain the search term in the title or the description.
     * @param $searchTerm string The search term
     * @return [Group]
     */
    public function searchGroups($searchTerm)
    {
        // TODO: Implement searchGroups() method.
    }
}
