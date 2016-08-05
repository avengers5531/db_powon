<?php

namespace Powon\Services;

use Powon\Entity\Event;

interface EventService
{
    const EVENT_TITLE = 'event_title';
    const EVENT_DESCRIPTION = 'event_description';

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
}