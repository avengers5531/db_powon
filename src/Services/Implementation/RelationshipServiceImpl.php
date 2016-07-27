<?php

namespace Powon\Services;

use Powon\Entity\Member;
use Powon\Entity\FriendRequest;
use Powon\Services\RelationshipService;
use Psr\Log\LoggerInterface;

interface RelationshipService{

    /**
     * @var MemberDAO
     */
    private $relationshipDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger, RelationshipDAO $dao)
    {
        $this->relationshipDAO = $dao;
        $this->log = $logger;
    }


    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function requestRelationship($member_from, $member_to, $rel_type){
        try{
            return $this->relationshipDAO->requestRelationship($member_from, $member_to, $rel_type);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return null;
        }
    }

    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    */
    public function confirmRelationship($member_from, $member_to){
        try{
            return $this->relationshipDAO->confirmRelationship($member_from, $member_to);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return null;
        }
    }

    /**
    * Get pending relationship requests for a specific member
    * @param mid int: member id of the requested party
    * @return array of members with the requested relationship type
    */
    public function getPendingRelRequests($mid){
        try{
            return $this->relationshipDAO->getPendingRelRequests($mid);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
    }

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @return string relationship if exists, else null
    */
    public function checkRelationship($mid1, $mid2){
        try{
            return $this->relationshipDAO->checkRelationship($mid1, $mid2);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return null;
        }
    }

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function updateRelationship($mid1, $mid2, $rel_type){
        try{
            return $this->relationshipDAO->updateRelationship($mid1, $mid2, $rel_type);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return null;
        }
    }
}
