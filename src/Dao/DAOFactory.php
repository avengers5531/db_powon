<?php

namespace Powon\Dao;

//require_once __DIR__ . '/Implementation/MemberDAOImpl.php';
use \Powon\Dao\Implementation\MemberDAOImpl as MemberDAOImpl;

class DAOFactory
{
    /**
     * @var PDO instance
     */
    private $conn;
    
    public function __construct(\PDO $pdo) 
    {
        $this->conn = $pdo;
    }
    
    public function getMemberDAO() {
       return new MemberDaoImpl($this->conn); 
    }
}