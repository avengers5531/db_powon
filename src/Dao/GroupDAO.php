<?php

namespace Powon\Dao;

use Powon\Entity\Group;

interface GroupDAO {

    /**
     * @param $id
     * @return Group|null
     */
    public function getGroupById($id);

    /**
     * @param $entity
     * @return Group
     */
    public function createNewGroup($entity);

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
}