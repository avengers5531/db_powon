<?php

namespace Powon\Services\Implementation;

use Powon\Entity\Member;
use Powon\Entity\FriendRequest;
use Powon\Services\RelationshipService;
use Psr\Log\LoggerInterface;
use Powon\DAO\RelationshipDAO;

class RelationshipServiceImpl implements RelationshipService{

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
    public function requestRelationship(Member $member_from, Member $member_to, $rel_type){
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
    public function confirmRelationship(Member $member_from, Member $member_to){
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
    public function getPendingRelRequests(Member $member){
        try{
            return $this->relationshipDAO->getPendingRelRequests($member);
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
    public function checkRelationship(Member $member1, Member $member2){
        try{
            $relationship = $this->relationshipDAO->checkRelationship($member1, $member2);
            switch ($relationship["relation_type"]){
                case 'F':
                    $relationship["relation_type"] = "Friends";
                    break;
                case 'I':
                    $relationship["relation_type"] = "Immediate Family";
                    break;
                case 'E':
                    $relationship["relation_type"] = "Extended Family";
                    break;
                case 'C':
                    $relationship["relation_type"] = "Colleagues";
                    break;
            }
            return $relationship;
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
    public function updateRelationship(Member $member1, Member $member2, $rel_type){
        try{
            return $this->relationshipDAO->updateRelationship($member1, $member2, $rel_type);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return null;
        }
    }

    /**
    * @param member1 Member
    * @param member2 Member
    */
    public function deleteRelationship(Member $member1, Member $member2){
        try{
            return $this->relationshipDAO->deleteRelationship($member1, $member2);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return null;
        }
    }

    /**
    * @param member Member: the member to search for friends
    * @param rel_type String: either F, I, E, or C
    * @return list of FriendRequest objects
    */
    public function getRelatedMembers(Member $member, $rel_type){
        try{
            return $this->relationshipDAO->getRelatedMembers($member, $rel_type);
        }
        catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: $ex->getMessage()");
            return [];
        }
    }

}
