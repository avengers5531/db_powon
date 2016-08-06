<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Event;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;
use Powon\Dao\EventDAO;
use Powon\Services\EventService;

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
            $msg = 'Invalid parameters entered.';
            $this->log->debug("Registration failed: $msg", $paramsRequest);
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
                $this->log->info('Created new event details: ',
                    ['title' => $paramsRequest[EventService::EVENT_TITLE]]);
                return array('success' => true,
                    'message' => 'New event details "' . $paramsRequest[EventService::EVENT_TITLE] . '" was created.');
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
     * @return Event|null
     */
    public function getEventDetailsById($event_id)
    {
        try{
            return $this->eventDAO->getEventDetails($event_id);
        } catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return null;
        }
    }
}