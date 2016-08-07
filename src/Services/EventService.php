<?php

namespace Powon\Services;

use Powon\Entity\Event;

interface EventService
{
    const EVENT_TITLE = 'event_title';
    const EVENT_DESCRIPTION = 'event_description';
    const EVENT_DATE = 'event_date';
    const EVENT_TIME = 'event_time';
    const EVENT_LOCATION = 'event_location';

    /**
     * @param $group_id
     * @param $paramsRequest array The http request body.
     * It should contain self::EVENT_TITLE and self::EVENT_DESCRIPTION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function createGroupEvent($group_id, $paramsRequest);

    /**
     * @param $group_id
     * @return Event[]
     */
    public function getEventsForGroup($group_id);

    /**
     * @param $event_id
     * @param $paramsRequest array The http request body.
     * It should contain self::EVENT_DATE, self::EVENT_TIME and self::EVENT_LOCATION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function addEventDetails($event_id, $paramsRequest);

    /**
     * @param $event_id
     * @return Event|null
     */
    public function getEventById($event_id);

    /**
     * @param $event_id
     * @return Event[]
     */
    public function getEventDetailsById($event_id);

    /**
     * @param $event_id
     * @param $member_id
     * @param $group_id
     * @param $paramsRequest array The http request body.
     * It should contain self::EVENT_DATE, self::EVENT_TIME and self::EVENT_LOCATION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function voteOnEventDetails($event_id, $member_id, $group_id, $paramsRequest);

    /**
     * @param $event
     * @return int
     */
    public function getVoteCounts($event);

    /**
     * @param $event_id
     * @return bool
     */
    public function deleteEvent($event_id);
}