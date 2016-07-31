<?php

namespace Powon\Dao;

use Powon\Dao\Implementation\GroupDAOImpl;
use Powon\Dao\Implementation\InterestDAOImpl;
use Powon\Dao\Implementation\MemberDAOImpl;
use Powon\Dao\Implementation\ProfessionDAOImpl;
use Powon\Dao\Implementation\RegionDAOImpl;
use Powon\Dao\Implementation\GroupPageDAOImpl;
use Powon\Dao\Implementation\IsGroupMemberDAOImpl;
use Powon\Dao\Implementation\SessionDAOImpl;
use Powon\Dao\Implementation\RelationshipDAOImpl;

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
       return new MemberDAOImpl($this->conn);
    }

    /**
     * @return MemberPageDAO
     */
    public function getMemberPageDAO() {
       return new MemberPageDAOImpl($this->conn);
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
    public function getInterestDAO()
    {
            return new InterestDAOImpl($this->conn);
    }
    /**
     * @return ProfessionDAOImpl
     */
    public function getProfessionDao()
    {
            return new ProfessionDAOImpl($this->conn);
    }
    /**
     * @return RegionDAOImpl
     */
    public function getRegionDAO()
    {
            return new RegionDAOImpl($this->conn);
    }
    /**
     * @return GroupDAO
     */
    public function getGroupDAO(){
        return new GroupDAOImpl($this->conn);
    }

    /**
     * @return IsGroupMemberDAO
     */
    public function getIsGroupMemberDAO(){
        return new IsGroupMemberDAOImpl($this->conn);
    }

    /**
     * @return GroupPageDAOImpl
     */
    public function getGroupPageDao()
    {
        return new GroupPageDAOImpl($this->conn);
    }
    
    /**
     * @return RelationshipDAOImpl
     */
    public function getRelationshipDAO()
    {
        return new RelationshipDAOImpl($this->conn);

    }
}
