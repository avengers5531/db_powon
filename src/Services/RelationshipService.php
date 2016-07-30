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
    public function requestRelationship(Member $member_from, Member $member_to, $rel_type);

    /**
    * @param member_from integer: the ID of the requesting member
    * @param member_to integer: the ID of the requested member
    */
    public function confirmRelationship(Member $member_from, Member $member_to);

    /**
    * Get pending relationship requests for a specific member
    * @param mid int: member id of the requested party
    * @return array of members with the requested relationship type
    */
    public function getPendingRelRequests(Member $member);

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @return string relationship if exists, else null
    */
    public function checkRelationship(Member $member1, Member $member2);

    /**
    * @param mid2 int, the id of the first member
    * @param mid2 int, the id of the second member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function updateRelationship(Member $member1, Member $member2, $rel_type);

    /**
    * @param member1 Member
    * @param member2 Member
    */
    public function deleteRelationship(Member $member1, Member $member2);



}
