<?php

namespace Powon\Dao;

use Powon\Entity\Event;
use Powon\Entity\Group;
use Powon\Entity\Member;

interface EventDAO {

    /**
     * @param $id
     * @return Event|null
     */
    public function getEventById($id);

    /**
     * @param $group_id
     * @return Event[]|null
     */
    public function getEventsForGroup($group_id);

    /**
     * @param $event
     * @return int
     */
    public function createEvent($event);

    /**
     * @param $event
     * @return bool
     */
    public function addEventDetails($event);

    /**
     * @param $event_id
     * @return Event[]|null
     */
    public function getEventDetails($event_id);
}