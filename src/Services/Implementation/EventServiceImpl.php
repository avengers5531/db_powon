<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Event;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;
use Powon\Dao\EventDAO;
use Powon\Services\EventService;
use Powon\Utils\DateTimeHelper;

use Slim\Http\UploadedFile;


class EventServiceImpl implements EventService
{
    /**
     * @var EventDAO
     */
    private $eventDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger, EventDAO $dao)
    {
        $this->log = $logger;
        $this->eventDAO = $dao;
    }


    /**
     * @param $group_id
     * @param $paramsRequest array The http request body.
     * It should contain self::EVENT_TITLE and self::EVENT_DESCRIPTION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function createGroupEvent($group_id, $paramsRequest)
    {
        $msg = '';
        if(!Validation::validateParametersExist(
            [EventService::EVENT_TITLE,
                EventService::EVENT_DESCRIPTION], $paramsRequest)){
            $msg = 'Invalid parameters entered.';
            $this->log->debug("Registration failed: $msg", $paramsRequest);
        }
        $data = array(
            'title' => $paramsRequest[EventService::EVENT_TITLE],
            'description' => $paramsRequest[EventService::EVENT_DESCRIPTION],
            'powon_group_id' => $group_id
        );
        $newEvent = new Event($data);
        try{
            $event_id = $this->eventDAO->createEvent($newEvent);
            if($event_id > 0){
                $this->log->info('Created new event: ',
                    ['title' => $paramsRequest[EventService::EVENT_TITLE]]);
                return array('success' => true,
                    'message' => 'New event "' . $paramsRequest[EventService::EVENT_TITLE] . '" was created.');
            }

        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when creating a new event: ". $ex->getMessage());
        }
        return array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    /**
     * @param $group_id
     * @return Event[]
     */
    public function getEventsForGroup($group_id)
    {
        try{
            return $this->eventDAO->getEventsForGroup($group_id);
        }catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * @param $event_id
     * @param $paramsRequest array The http request body.
     * It should contain self::EVENT_DATE, self::EVENT_TIME and self::EVENT_LOCATION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function addEventDetails($event_id, $paramsRequest)
    {
        $msg = '';
        if(!Validation::validateParametersExist(
            [EventService::EVENT_DATE,
                EventService::EVENT_TIME, 
                EventService::EVENT_LOCATION], $paramsRequest)){
            $msg = 'Missing information.';
            $this->log->debug("Registration failed: $msg", $paramsRequest);
            return array(
                'success' => false,
                'message' => $msg
            );
        }
        if(!DateTimeHelper::validateDateFormat($paramsRequest[EventService::EVENT_DATE])){
            $msg = 'Wrong date format.';
            $this->log->debug("Invalid date format ", $paramsRequest);
            return array(
                'success' => false,
                'message' => $msg
            );
        }
        if(!DateTimeHelper::validateTimeFormat($paramsRequest[EventService::EVENT_TIME])){
            $msg = 'Wrong time format.';
            $this->log->debug("Invalid time format ", $paramsRequest);
            return array(
                'success' => false,
                'message' => $msg
            );
        }
        $data = array(
            'event_id' => $event_id,
            'event_date' => $paramsRequest[EventService::EVENT_DATE],
            'event_time' => $paramsRequest[EventService::EVENT_TIME],
            'location' => $paramsRequest[EventService::EVENT_LOCATION]
        );
        $newEventDetails = new Event($data);
        try{
            if($this->eventDAO->addEventDetails($newEventDetails)){
                $this->log->info('Created new event details.');
                return array('success' => true,
                    'message' => 'New event details was created.');
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when creating a new event details: ". $ex->getMessage());
        }
        return array(
            'success' => false,
            'message' => 'Something went wrong!'
        );
    }

    /**
     * @param $event_id
     * @return Event|null
     */
    public function getEventById($event_id)
    {
        try{
            return $this->eventDAO->getEventById($event_id);
        } catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return null;
        }
    }

    /**
     * @param $event_id
     * @return Event[]
     */
    public function getEventDetailsById($event_id)
    {
        try{
            return $this->eventDAO->getEventDetails($event_id);
        } catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * @param $event_id
     * @param $member_id
     * @param $group_id
     * @param $paramsRequest array The http request body.
     * It should contain self::EVENT_DATE, self::EVENT_TIME and self::EVENT_LOCATION keys.
     * @return array ['success' => bool, 'message' => string]
     */
    public function voteOnEventDetails($event_id, $member_id, $group_id, $paramsRequest)
    {
        $msg = '';
        if(!Validation::validateParametersExist(
            [EventService::EVENT_DATE,
                EventService::EVENT_TIME,
                EventService::EVENT_LOCATION], $paramsRequest)){
            $msg = 'Invalid parameters entered.';
            $this->log->debug("Registration failed: $msg", $paramsRequest);
        }
        $data = array(
            'powon_group_id' => $group_id,
            'event_id' => $event_id,
            'event_date' => $paramsRequest[EventService::EVENT_DATE],
            'event_time' => $paramsRequest[EventService::EVENT_TIME],
            'location' => $paramsRequest[EventService::EVENT_LOCATION]
        );
        $voteOnEventDetails = new Event($data);
        try{
            if($this->eventDAO->voteOnEventDetail($member_id, $voteOnEventDetails)){
                $this->log->info('Member ' . $member_id . ' voted on event '. $event_id);
                return array('success' => true,
                    'message' => 'Member ' . $member_id . ' voted on event '. $event_id);
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when creating a new event details: ". $ex->getMessage());
        }
        return array(
            'success' => false,
            'message' => 'Cannot vote again!'
        );
    }

    /**
     * @param $event Event
     * @return int
     */
    public function getVoteCounts($event)
    {
        try{
            $res = $this->eventDAO->countVotes($event);
            $this->log->info('Received vote counts');
            return $res;

        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when getting vote counts: ". $ex->getMessage());
        }
        return 0;
    }

    /**
     * @param $event_id
     * @return bool
     */
    public function deleteEvent($event_id)
    {
        try{
            if($this->eventDAO->deleteEvent($event_id)){
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting event: ". $ex->getMessage());
        }
        return false;
    }
}