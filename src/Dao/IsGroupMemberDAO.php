<?php

namespace Powon\Dao;


use Powon\Entity\Member;

interface IsGroupMemberDAO {

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberBelongsToGroup($member_id, $group_id);

    /**
     * Checks whether a member is waiting for an approval
     * @param $member_id int the member id
     * @param $group_id int the group id
     * @return bool True if member is waiting for approval, false otherwise
     */
    public function memberWaitingForApprovalToGroup($member_id, $group_id);
    
    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberRequestsToJoinGroup($member_id, $group_id);

    /**
     * @param $group_id
     * @return Member[]
     */
    public function membersWaitingApproval($group_id);

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function acceptMemberIntoGroup($member_id, $group_id);

    /**
     * @param $group_id
     * @return Member[]
     */
    public function membersInGroup($group_id);

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function deleteMemberFromGroup($member_id, $group_id);
}
