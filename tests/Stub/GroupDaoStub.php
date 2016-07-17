<?php

namespace Powon\Test\Stub;


use Powon\Dao\GroupDAO;
use Powon\Dao\IsGroupMemberDAO;
use Powon\Dao\Member;
use Powon\Entity\Group;

class GroupDaoStub implements GroupDAO, IsGroupMemberDAO
{
    /**
     * @var array of mock group data.
     * @var array of members in groups data.
     */
    public $groups;
    public $isGroupMember;
    public $members;

    public function __construct()
    {
        $this->groups = [];
        $this->isGroupMember = [];
        $this->members = [];
    }

    /**
     * @param $id
     * @return Group|null
     */
    public function getGroupById($id)
    {
        for ($i = 0; $i < count($this->groups); $i++) {
            if ($this->groups[$i]['powon_group_id'] == $id) {
                return new Group($this->groups[$i]);
            }
        }
        return null;
    }

    /**
     * @param $group Group
     * @return bool
     */
    public function createNewGroup($group)
    {
        $new_group = $group->toObject();
        $this->groups[] = $new_group;
        return true;
    }

    /**
     * @param $owner_id
     * @return Group[]|null
     */
    public function getGroupByOwnerId($owner_id)
    {
        $resultArray = new Group();
        for ($i = 0; $i < count($this->groups); $i++) {
            if ($this->groups[$i]['group_owner'] == $owner_id) {
                array_push($resultArray, $this->groups[$i]);
            }
        }
        if(!empty($resultArray)){
            return $resultArray;
        }
        else{
            return null;
        }
    }

    /**
     * @param $input
     * @return Group[]|null
     */
    public function searchGroupByTitle($input)
    {
        $resultArray = new Group();
        for ($i = 0; $i < count($this->groups); $i++) {
            if ((strcmp($this->groups[$i]['group_title'],$input) === 0) ||
                    (strcmp($this->groups[$i]['description'],$input) === 0)) {
                array_push($resultArray, $this->groups[$i]);
            }
        }
        if(!empty($resultArray)){
            return $resultArray;
        }
        else{
            return null;
        }
    }

    /**
     * @param $id
     * @return Group[]|null
     */
    public function getGroupsMemberBelongsTo($id)
    {
        $resultArray = new Group();
        for ($i = 0; $i < count($this->isGroupMember); $i++) {
            if ($this->isGroupMember[$i]['member_id'] == $id) {
                for ($j = 0; $j < count($this->groups); $j++) {
                    if($this->groups[$j]['powon_group_id'] == $this->isGroupMember[$i]['powon_group_id']){
                        array_push($resultArray, $this->groups[$j]);
                    }
                }
            }
        }
        if(!empty($resultArray)){
            return $resultArray;
        }
        else{
            return null;
        }
    }

    /**
     * @param $id
     * @return Group[]|null
     */
    public function getGroupsMemberNotBelongsTo($id)
    {
        $resultArray = new Group();
        for ($i = 0; $i < count($this->isGroupMember); $i++) {
            if ($this->isGroupMember[$i]['member_id'] == $id) {
                for ($j = 0; $j < count($this->groups); $j++) {
                    if($this->groups[$j]['powon_group_id'] != $this->isGroupMember[$i]['powon_group_id']){
                        array_push($resultArray, $this->groups[$j]);
                    }
                }
            }
        }
        if(!empty($resultArray)){
            return $resultArray;
        }
        else{
            return null;
        }
    }

    /**
     * @param $id
     * return bool
     */
    public function deleteGroup($id)
    {
        for ($i = 0; $i < count($this->groups); $i++) {
            if ($this->groups[$i]['powon_group_id'] == $id) {
                    array_splice($this->groups[$i], $i, 1);
                    return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupTitle($id, $input)
    {
        for ($i = 0; $i < count($this->groups); $i++) {
            if($this->groups[$i]['powon_group_id'] == $id){
                $this->groups[$i]['group_title'] = $input;
                return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupDescription($id, $input)
    {
        for ($i = 0; $i < count($this->groups); $i++) {
            if($this->groups[$i]['powon_group_id'] == $id){
                $this->groups[$i]['description'] = $input;
                return true;
            }
        }
        return false;
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberBelongsToGroup($member_id, $group_id)
    {
        for ($i = 0; $i < count($this->isGroupMember); $i++) {
            if ($this->isGroupMember[$i]['member_id'] == $member_id &&
                $this->isGroupMember[$i]['powon_group_id'] == $group_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function memberRequestsToJoinGroup($member_id, $group_id)
    {
        // TODO: Implement memberRequestsToJoinGroup() method.
    }

    /**
     * @param $group_id
     * @return Member[]
     */
    public function membersWaitingApproval($group_id)
    {
        $resultArray = new Member();
        for ($i = 0; $i < count($this->isGroupMember); $i++) {
            if ($this->isGroupMember[$i]['powon_group_id'] == $group_id) {
                for ($j = 0; $j < count($this->members); $j++) {
                    if($this->members[$j]['member_id'] == $this->isGroupMember[$i]['member_id']){
                        array_push($resultArray, $this->members[$j]);
                    }
                }
            }
        }
        if(!empty($resultArray)){
            return $resultArray;
        }
        else{
            return null;
        }
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function acceptMemberIntoGroup($member_id, $group_id)
    {
        // TODO: Implement acceptMemberIntoGroup() method.
    }

    /**
     * @param $group_id
     * @return Member[]
     */
    public function membersInGroup($group_id)
    {
        $resultArray = new Member();
        for ($i = 0; $i < count($this->isGroupMember); $i++) {
            if ($this->isGroupMember[$i]['powon_group_id'] == $group_id) {
                for ($j = 0; $j < count($this->members); $j++) {
                    if($this->members[$j]['member_id'] == $this->isGroupMember[$i]['member_id']){
                        array_push($resultArray, $this->members[$j]);
                    }
                }
            }
        }
        if(!empty($resultArray)){
            return $resultArray;
        }
        else{
            return null;
        }
    }

    /**
     * @param $member_id
     * @param $group_id
     * @return bool
     */
    public function deleteMemberFromGroup($member_id, $group_id)
    {
        for ($i = 0; $i < count($this->isGroupMember); $i++) {
            if ($this->isGroupMember[$i]['powon_group_id'] == $group_id) {
                if($this->isGroupMember[$i]['member_id'] == $group_id){
                    array_splice($this->isGroupMember[$i], $i, 1);
                    return true;
                }
            }
        }
        return false;
    }
}