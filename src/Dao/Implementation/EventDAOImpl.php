<?php
/**
 * Created by IntelliJ IDEA.
 * User: Devang
 * Date: 2016-08-05
 * Time: 1:09 AM
 */

namespace Powon\Dao\Implementation;


use Powon\Dao\EventDAO;
use Powon\Entity\Event;

class EventDAOImpl implements EventDAO
{
    private $db;

    /**
     * EvenDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }


    /**
     * @param $id
     * @return Event|null
     */
    public function getEventById($id)
    {
        $sql = 'SELECT *
                FROM event
                WHERE event_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Event($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $group_id
     * @return Event[]|null
     */
    public function getEventsForGroup($group_id)
    {
        $sql = 'SELECT *
                FROM event
                WHERE powon_group_id = :group_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $group_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
            return array_map(function($data) {
                return new Event($data);
            }, $rows);
        } else {
            return [];
        }
    }

    /**
     * @param $event Event
     * @return int
     */
    public function createEvent($event)
    {
        $sql = 'INSERT INTO event (title, description, powon_group_id) VALUES(:title, :description, :group_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $event->getEventTitle(), \PDO::PARAM_STR);
        $stmt->bindValue(':description', $event->getEventDescription(), \PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $event->getGroupId(), \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return 0;
    }

    /**
     * @param $event Event
     * @return bool
     */
    public function addEventDetails($event)
    {
        $sql = 'INSERT INTO event_details (event_id, event_date, event_time, location) VALUES(:event_id, :event_date, :event_time, :location)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':event_id', $event->getEventId(), \PDO::PARAM_STR);
        $stmt->bindValue(':event_date', $event->getEventDate(), \PDO::PARAM_STR);
        $stmt->bindValue(':event_time', $event->getEventTime(), \PDO::PARAM_STR);
        $stmt->bindValue(':location', $event->getEventLocation(), \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * @param $event_id
     * @return Event[]|null
     */
    public function getEventDetails($event_id)
    {
        $sql = 'SELECT e.event_date,
                       e.event_time,
                       e.location,
                       e.event_id
               FROM event_details e
               WHERE e.event_id = :event_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':event_id', $event_id, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
            return array_map(function($data) {
                return new Event($data);
            }, $rows);
        } else {
            return [];
        }
    }

    /**
     * @param $member_id
     * @param $event Event
     * @return bool
     */
    public function voteOnEventDetail($member_id, $event)
    {
        $sql = 'INSERT INTO votes_on (member_id, powon_group_id, event_id, event_date, event_time, location) 
                  VALUES (:member_id, :powon_group_id, :event_id, :event_date, :event_time, :location)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_STR);
        $stmt->bindValue(':powon_group_id', $event->getGroupId(), \PDO::PARAM_STR);
        $stmt->bindValue(':event_id', $event->getEventId(), \PDO::PARAM_STR);
        $stmt->bindValue(':event_date', $event->getEventDate(), \PDO::PARAM_STR);
        $stmt->bindValue(':event_time', $event->getEventTime(), \PDO::PARAM_STR);
        $stmt->bindValue(':location', $event->getEventLocation(), \PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * @param $event Event
     * @return int
     */
    public function countVotes($event)
    {
        $sql = 'SELECT COUNT(*) AS voteCount
                FROM votes_on v
                WHERE v.event_date = :event_date AND v.event_time = :event_time AND v.location = :location';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':event_date', $event->getEventDate(), \PDO::PARAM_STR);
        $stmt->bindValue(':event_time', $event->getEventTime(), \PDO::PARAM_STR);
        $stmt->bindValue(':location', $event->getEventLocation(), \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $rows = $stmt->fetch();
            return (int)$rows['voteCount'];
        } else {
            return 0;
        }
    }

    /**
     * @param $event_id
     * @return bool
     */
    public function deleteEvent($event_id)
    {
        $sql = 'DELETE FROM event WHERE event_id = :event_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':event_id', $event_id, \PDO::PARAM_STR);
        return $stmt->execute();
    }
}