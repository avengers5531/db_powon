<?php
/**
 * Created by IntelliJ IDEA.
 * User: link
 * Date: 2016-06-18
 * Time: 6:05 PM
 */
require_once __DIR__.'/implementation/MemberDAOImpl.php';

class DAOFactory
{
    /**
     * @var PDO instance
     */
    private $conn;
    
    public function __construct(PDO $pdo) 
    {
        $this->conn = $pdo;
    }
    
    public function getMemberDAO() {
       return new MemberDaoImpl($this->conn); 
    }
}