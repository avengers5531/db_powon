<?php

namespace Powon\Dao;

use Powon\Dao\Implementation\InterestDAOImpl;
use Powon\Dao\Implementation\MemberDaoImpl;
use Powon\Dao\Implementation\SessionDAOImpl;

class DAOFactory
{
    /**
     * @var \PDO instance
     */
    private $conn;
    
    public function __construct(\PDO $pdo) 
    {
        $this->conn = $pdo;
    }

    /**
     * @return MemberDAO
     */
    public function getMemberDAO() {
       return new MemberDaoImpl($this->conn); 
    }

    /**
     * @return SessionDAO
     */
    public function getSessionDAO() {
        return new SessionDAOImpl($this->conn);
    }

    /**
     * @return InterestDAOImpl
     */
    public function getInterestDAO() {
        return new InterestDAOImpl($this->conn);
    }
}
