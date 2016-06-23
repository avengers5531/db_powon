<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\MemberDAO as MemberDAO;
use \Powon\Entity\Member as Member;

class MemberDaoImpl implements MemberDAO {

    private $db;

    /**
     * MemberDaoImpl constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo; 
    }

    /**
     * @return array of member entities.
     */
    public function getAllMembers()
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth
        FROM member m';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        /*$results = [];
        while($row = $stmt->fetch()) {
            $results[] = new MemberEntity($row);
        }
        return $results;*/
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }
}