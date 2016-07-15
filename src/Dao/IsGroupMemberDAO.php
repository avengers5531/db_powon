<?php

namespace Powon\Dao;

use Powon\Entity\Is_group_member;

interface IsGroupMemberDAO {

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberBelongsToGroup($member_id, $group_id);
    
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