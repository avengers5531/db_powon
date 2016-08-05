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
}