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
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
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
        $sql = '';
    }

    /**
     * @param $group_id
     * @return int
     */
    public function createEvent($group_id)
    {
        // TODO: Implement createEvent() method.
    }
}