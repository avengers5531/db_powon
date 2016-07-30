<?php

namespace Powon\Dao;

use Powon\Entity\FriendRequest;
use Powon\Entity\Member;

interface RelationshipDAO{
    /**
    * @param member_from Member: the requesting member
    * @param member_to Member: the requested member
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function requestRelationship(Member $member_from, Member $member_to, $rel_type);

    /**
    * @param member_from Member: the requesting member
    * @param member_to Member: the requested member
    */
    public function confirmRelationship(Member $member_from, Member $member_to);

    /**
    * Get pending relationship requests for a specific member
    * @param mid int: member id of the requested party
    * @return array of members with the requested relationship type
    */
    public function getPendingRelRequests(Member $member);

    /**
    * @param member1 Member, the id of the first member
    * @param member2 Member, the second member (order is irrelevant)
    * @return string relationship if exists, else null
    */
    public function checkRelationship(Member $member1, Member $member2);

    /**
    * @param member1 member, the first member
    * @param member2 member, the second member (order of members is irrelevant)
    * @param rel_type string (single character): the relationship type ('F', 'I', 'E', 'C')
    */
    public function updateRelationship(Member $member1, Member $member2, $rel_type);

    /**
    * @param member1 Member
    * @param member2 Member
    */
    public function deleteRelationship(Member $member1, Member $member2);
}
