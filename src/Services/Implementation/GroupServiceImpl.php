<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Group;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;
use Powon\Dao\GroupDAO;
use Powon\Dao\IsGroupMemberDAO;
use Powon\Services\GroupService;

class GroupServiceImpl implements GroupService
{
    /**
     * @var GroupDAO
     */
    private $groupDAO;
    private $isGroupMemberDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger, GroupDAO $dao, IsGroupMemberDAO $dao2)
    {
        $this->groupDAO = $dao;
        $this->isGroupMemberDAO = $dao2;
        $this->log = $logger;
    }

    /**
     * @param $member_id int the member's id
     * @param $group_id int the group id.
     * @return bool True if member belongs to group, false otherwise
     */
    public function memberBelongsToGroup($member_id, $group_id)
    {
        if($this->groupDAO->memberBelongsToGroup($member_id, $group_id)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Retrieves all the groups a member belongs to
     * @param $member_id int The member's group
     * @return [Group] array of Group entities
     */
    public function getGroupsMemberBelongsTo($member_id)
    {
        try {
            return $this->groupDAO->getGroupsMemberBelongsTo($member_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
    }

    /**
     * All groups minus the ones member belongs to
     * @param $member_id int The member's group
     * @return [Group] array of Group entities
     */
    public function getGroupsMemberDoesNotBelongTo($member_id)
    {
        try {
            return $this->groupDAO->getGroupsMemberNotBelongsTo($member_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
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
        if (!Validation::validateParametersExist(
            [GroupService::GROUP_TITLE, GroupService::GROUP_DESCRIPTION],
            $paramsRequest)
        ) {
            return ['success' => false, 'message' => 'Invalid parameters!'];
        }
        $data = array(
            'group_title' => $paramsRequest[GroupService::GROUP_TITLE],
            'description' => $paramsRequest[GroupService::GROUP_DESCRIPTION],
            'group_owner' => $member_id
        );
        $newGroup = new Group($data);

        try {
            if ($this->groupDAO->createNewGroup($newGroup)) {
                $this->log->info('Created new group',
                    ['group_title' => $paramsRequest[GroupService::GROUP_TITLE]]);
                return array('success' => true,
                    'message' => 'New Group '.$paramsRequest[GroupService::GROUP_DESCRIPTION].' was created.');
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when creating a new group: $ex->getMessage()");
        }
        return array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
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
        if(empty($paramsRequest[1]) && !empty($paramsRequest[0])) {
            $this->groupDAO->updateGroupTitle($group_id, $paramsRequest[0]);
            return true;
        }
        elseif(empty($paramsRequest[0]) && !empty($paramsRequest[1])) {
            $this->groupDAO->updateGroupDescription($group_id, $paramsRequest[1]);
            return true;
        }
        elseif(!empty($paramsRequest[0]) && !empty($paramsRequest[1])) {
            $this->groupDAO->updateGroupTitle($group_id, $paramsRequest[0]);
            $this->groupDAO->updateGroupDescription($group_id, $paramsRequest[1]);
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Deletes the group with given group id
     * @param $group_id
     * @return bool true on success, false on failure
     */
    public function deleteGroup($group_id)
    {
        try{
            if($this->groupDAO->deleteGroup($group_id)){
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting group: $ex->getMessage()");
        }
        return false;
    }

    /**
     * Creates a request in the database
     * @param $requestor_id int The id from the member who submitted the request.
     * @param $group_id int The group id.
     * @return bool true on success, false otherwise
     */
    public function createRequestToJoinGroup($requestor_id, $group_id)
    {
        try{
            if($this->isGroupMemberDAO->memberRequestsToJoinGroup($requestor_id, $group_id)){
                $this->log->info('Member with Id ' . $requestor_id . ' sent request to be 
                    in group with id ' . $group_id);
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when requesting group membership: $ex->getMessage()");
        }
        return false;
    }

    /**
     * Updates the request with an approval date. This should be called by a group owner.
     * @param $requestor_id
     * @param $group_id
     * @return bool true on success, false otherwise.
     */
    public function acceptRequestToJoinGroup($requestor_id, $group_id)
    {
        try{
            if($this->isGroupMemberDAO->acceptMemberIntoGroup($requestor_id, $group_id)){
                $this->log->info('Member with Id ' . $requestor_id . ' was accepted as member of
                    group with id ' . $group_id);
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when accepting member into group: $ex->getMessage()");
        }
        return false;
    }

    /**
     * Returns an array of member entities who have submitted a request to join the group
     * but haven't received approval.
     * @param $group_id int The group id.
     * @return [Member]
     */
    public function getMembersWithPendingRequestsToGroup($group_id)
    {
        try {
            return $this->isGroupMemberDAO->membersWaitingApproval($group_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
    }

    /**
     * Removes a member from a group.
     * @param $member_id int The member's id to remove
     * @param $group_id int The group id
     * @return bool true on success, false otherwise
     */
    public function removeMemberFromGroup($member_id, $group_id)
    {
        try{
            if($this->isGroupMemberDAO->deleteMemberFromGroup($member_id, $group_id)){
                $this->log->info('Member with Id ' . $member_id . ' was deleted as member of
                    group with id ' . $group_id);
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting member from group: $ex->getMessage()");
        }
        return false;
    }

    /**
     * Gets all the members belonging to a group.
     * @param $group_id int The group id.
     * @return [Member]
     */
    public function getGroupMembers($group_id)
    {
        try {
            return $this->isGroupMemberDAO->membersInGroup($group_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
    }

    /**
     * Gets a list of Group entities that contain the search term in the title or the description.
     * @param $searchTerm string The search term
     * @return [Group]
     */
    public function searchGroups($searchTerm)
    {
        try {
            return $this->groupDAO->searchGroupByTitle($searchTerm);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
    }

    /**
     * @param $id int The group's id
     * @return Group|null
     */
    public function getGroupById($group_id)
    {
        try {
            return $this->groupDAO->getGroupById($group_id);
        } catch (\PDOException $ex) {
            $this->log->error("Exception occurred when retrieving group $group_id: $ex->getMessage()");
            return null;
        }
    }
}
