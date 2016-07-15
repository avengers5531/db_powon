<?php

namespace Powon\Test\Stub;


use Powon\Dao\GroupDAO;
use Powon\Entity\Group;

class GroupDaoStub implements GroupDAO
{
    /**
     * @var array of mock group data.
     * @var array of members in groups data.
     */
    public $groups;
    public $isGroupMember;

    public function __construct()
    {
        $this->groups = [];
        $this->isGroupMember = [];
    }

    /**
     * @param $id
     * @return Group|null
     */
    public function getGroupById($id)
    {
        for ($i = 0; $i < count($this->groups); $i++) {
            if ($this->groups[$i]['group_id'] == $id) {
                return new Group($this->groups[$i]);
            }
        }
        return null;
    }

    /**
     * @param $group
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
        for ($i = 0; $i < count($this->groups); $i++) {
            if ($this->groups[$i]['group_owner'] == $owner_id) {
                return new Group($this->groups[$i]);
            }
        }
        return null;
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
                    if($this->groups[$j]['group_id'] == $this->isGroupMember[$i]['group_id']){
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
                    if($this->groups[$j]['group_id'] != $this->isGroupMember[$i]['group_id']){
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
        // TODO: Implement deleteGroup() method.
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupTitle($id, $input)
    {
        // TODO: Implement updateGroupTitle() method.
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupDescription($id, $input)
    {
        // TODO: Implement updateGroupDescription() method.
    }
}