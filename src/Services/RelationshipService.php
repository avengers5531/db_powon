<?php

namespace Powon\Services;

use Powon\Entity\Member;
use Powon\Entity\FriendRequest;

interface RelationshipService{

    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function requestRelationship($member_from, $member_to, $rel_type);

    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    */
    public function confirmRelationship($member_from, $member_to);

    /**
    * Get pending relationship requests for a specific member
    * @param mid int: member id of the requested party
    * @return array of members with the requested relationship type
    */
    public function getPendingRelRequests($mid);

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @return string relationship if exists, else null
    */
    public function checkRelationship($mid1, $mid2);

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function updateRelationship($mid1, $mid2, $rel_type);


}
