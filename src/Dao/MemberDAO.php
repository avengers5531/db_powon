<?php

namespace Powon\Dao;

use Powon\Entity\Member;

interface MemberDAO {

    /**
     * @return Member[] of member entities.
     */
    public function getAllMembers();

    /**
     * @param int $id
     * @return Member|null
     */
    public function getMemberById($id);

    /**
     * @param string $username
     * @param bool $withPwd Set to true if you want the hashed password of the user.
     * @return Member|null
     */
    public function getMemberByUsername($username, $withPwd = false);

    /**
     * @param string $email
     * @param bool $withPwd set to true if you want the hashed password.
     * @return Member|null
     */
    public function getMemberByEmail($email, $withPwd = false);

    /**
     * @param $entity Member
     * @param $hashed_pwd string
     * @return bool
     */
    public function createNewMember($entity, $hashed_pwd);

    /**
     * @param $member_id Member : the Member entity with updated values
     * @return bool : true if update successful
     */
    public function updateMember($member);

    /**
     * @param $id
     * @return bool
     */
    //TODO: does this automatically delete member page?
    public function deleteMember($id);

    /**
     * @param string $name
     * @param Interest[] $interests
     * @return Member[] of member entities.
     */


    public function searchMembersByNameWithInterests($name,$interests);

    /**
     * @param string $name
     * @return Member[] of member entities.
     */
    public function searchMembersByName($name);

    /**
     * @return Member[] of member entities.
     */
    public function getNewMembers();

    /**
     * @param Interest[] $interests
     * @return Member[] of member entities.
     */
    public function getNewMembersWithInterests($interests);
}
