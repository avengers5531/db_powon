<?php

namespace Powon\Dao;

use Powon\Entity\Group;
use Powon\Entity\Member;

interface GroupDAO {

    /**
     * @param $id
     * @return Group|null
     */
    public function getGroupById($id);

    /**
     * @param $group
     * @return int The id of the newly created group
     */
    public function createNewGroup($group);

    /**
     * @param $owner_id
     * @return Group[]|null
     */
    public function getGroupByOwnerId($owner_id);

    /**
     * @param $input
     * @return Group[]|null
     */
    public function searchGroupByTitle($input);

    /**
     * @param $id
     * @return Group[]|null
     */
    public function getGroupsMemberBelongsTo($id);

    /**
     * @param $id
     * @return Group[]|null
     */
    public function getGroupsMemberNotBelongsTo($id);

    /**
     * @param $id
     * return bool
     */
    public function deleteGroup($id);

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupTitle($id, $input);

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function updateGroupDescription($id, $input);

    /**
     * @param $group Group entity
     * @return bool
     */
    public function updateGroupPicture($group);
}

