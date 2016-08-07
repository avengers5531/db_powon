<?php

namespace Powon\Dao\Implementation;

use \Powon\Dao\MemberDAO as MemberDAO;
use \Powon\Entity\Member as Member;

class MemberDAOImpl implements MemberDAO {

    private $db;

    /**
     * MemberDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return array of member entities.
     */
    public function getAllMembers()
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.is_admin,
                m.region_access,
                m.professions_access,
                m.status,
                m.email_access,
                m.dob_access,
                m.interests_access,
                m.registration_date
        FROM member m';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        /*$results = [];
        while($row = $stmt->fetch()) {
            $results[] = new MemberEntity($row);
        }
        return $results;*/
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }





    /**
     * @param int $id
     * @return Member|null
     */
    public function getMemberById($id)
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.is_admin,
                m.status,
                m.region_access,
                m.professions_access,
                m.interests_access,
                m.dob_access,
                m.email_access,
                m.profile_picture,
                m.registration_date
                FROM member m
                WHERE member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Member($row) : null);
        } else {
            return null;
        }
    }



    // TODO FIX this
    public function getregion($id)
    {
        $sql = 'select
        member.region_access as region_access
        , member.member_id as member_id ,
        region.region_id as region_id
        , region.country as country
         , region.province as province
          , region.city as city
           from member , region
        where
        member.lives_in=region.region_id
        and member.member_id=:id ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        /*$results = [];
        while($row = $stmt->fetch()) {
            $results[] = new MemberEntity($row);
        }
        return $results;*/
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }


    // TODO fixme
    public function getinterestss()
    {
        $sql = 'select
        member.interests_access as interests_access
        , member.member_id as member_id ,
        interestss.interestss_id as interestss_id
          , interestss.interestss_name as interestss_name
           from member , interestss
        where
        member.interests_access=interestss.interestss_id
        and member.member_id=:id ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        /*$results = [];
        while($row = $stmt->fetch()) {
            $results[] = new MemberEntity($row);
        }
        return $results;*/
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }




    // TODO fixme
    public function getprofessionn()
    {
        $sql = 'select
        member.professions_access as professions_access
        , member.member_id as member_id ,
        professionn.professionn_id as professionn_id
          , professionn.professionn_name as professionn_name
           from member , professionn
        where
        member.interests_access=professionn.professionn_id
        and member.member_id=:id ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        /*$results = [];
        while($row = $stmt->fetch()) {
            $results[] = new MemberEntity($row);
        }
        return $results;*/
        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }


    /**
     * @param string $username
     * @param bool $withPwd Set to true if you want the hashed password of the user.
     * @return Member|null
     */
    public function getMemberByUsername($username, $withPwd = false)
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.status,
                m.is_admin,
                m.region_access,
                m.professions_access,
                m.email_access,
                m.interests_access,
                m.profile_picture,'.
                ($withPwd? 'm.password, ' : ' ').
                'm.profile_picture,
                m.registration_date
                FROM member m
                WHERE m.username = :username';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Member($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param string $email
     * @param bool $withPwd set to true if you want the hashed password.
     * @return Member|null
     */
    public function getMemberByEmail($email, $withPwd = false)
    {
        $sql = 'SELECT m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.user_email,
                m.date_of_birth,
                m.status,
                m.is_admin,'.
                ($withPwd? 'm.password, ' : ' ').
                'm.profile_picture,
                m.registration_date
                FROM member m
                WHERE m.user_email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Member($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @param $member Member
     * @param $hashed_pwd string
     * @return bool
     */
    public function createNewMember($member, $hashed_pwd)
    {
        $sql = 'INSERT INTO member(username, password, user_email,
                first_name, last_name, date_of_birth, is_admin)
                VALUES
                (:username, :pwd, :email, :fname, :lname, :dob, :is_admin)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $member->getUsername(), \PDO::PARAM_STR);
        $stmt->bindValue(':pwd', $hashed_pwd, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $member->getUserEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(':fname', $member->getFirstName(), \PDO::PARAM_STR);
        $stmt->bindValue(':lname', $member->getLastName(), \PDO::PARAM_STR);
        $stmt->bindValue(':dob', $member->getDateOfBirth());
        $admin_val = $member->isAdmin() ? 'Y' : 'N';
        $stmt->bindValue(':is_admin', $admin_val, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * @param $member_id int : the ID of the member being updated
     * @param $attribute string : the Member attribute to be updated
     * @param $value string : the new value for the attribute
     * @return bool : true if update successful
     */
    public function updateMember($member)
    {
        $sql = 'UPDATE member SET user_email = :email,
                first_name = :fname,
                last_name = :lname,
                date_of_birth = :dob,
                is_admin = :admin,
                status = :status,
                profile_picture = :pic,
                dob_access = :dob_a,
                email_access = :email_a,
                interests_access = :int_a,
                professions_access = :prof_a,
                region_access = :reg_a
                WHERE member_id = :mid';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $member->getUserEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(':fname', $member->getFirstName(), \PDO::PARAM_STR);
        $stmt->bindValue(':lname', $member->getLastName(), \PDO::PARAM_STR);
        $stmt->bindValue(':dob', $member->getDateOfBirth());
        $stmt->bindValue(':admin', $member->isAdmin() ? "Y" : "N", \PDO::PARAM_BOOL);
        $stmt->bindValue(':status', $member->getStatus(), \PDO::PARAM_STR);
        $stmt->bindValue(':pic', $member->getProfilePic());
        $stmt->bindValue(':mid', $member->getMemberId(), \PDO::PARAM_STR);
        $stmt->bindValue(':dob_a', $member->getDobAccess(), \PDO::PARAM_INT);
        $stmt->bindValue(':email_a', $member->getEmailAccess(), \PDO::PARAM_INT);
        $stmt->bindValue(':int_a', $member->getInterestsAccess(), \PDO::PARAM_INT);
        $stmt->bindValue(':prof_a', $member->getProfessionsAccess(), \PDO::PARAM_INT);
        $stmt->bindValue(':reg_a', $member->getRegionAccess(), \PDO::PARAM_INT);
        return ($stmt->execute());
    }
    /**
     * @param $id
     * @return bool
     */
    // delete cascade automatically deletes member page and all their content
    public function deleteMember($id)
    {
        $sql = 'DELETE FROM member WHERE member_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param string $name
     * @param Interest[] $interests
     * @return Member[] of member entities.
     */
    public function searchMembersByNameWithInterests($name,$interests)
    {
        $interests_group = [];
        foreach ($interests as $interest) {
            $interests_group[] = $interest->getName();
        }
        $in = implode(',', array_fill(0, count($interests_group), '?'));

        $sql = "SELECT DISTINCT
                m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.registration_date,
                m.profile_picture
                FROM has_interests i
                JOIN member m ON i.member_id=m.member_id
                WHERE i.interest_name IN ($in) AND (CONCAT(m.first_name,' ',m.last_name) LIKE '$name%')
                ORDER BY m.registration_date DESC";
        $stmt = $this->db->prepare($sql);

        foreach ($interests_group as $k => $id)
            $stmt->bindValue(($k+1), $id);

        $stmt->execute();

        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }

    /**
     * @param string $name
     * @return Member[] of member entities.
     */
    public function searchMembersByName($name)
    {
        $sql = "SELECT
                m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.registration_date,
                m.profile_picture
                FROM member m
                WHERE CONCAT(m.first_name,' ',m.last_name) LIKE :name
                ORDER BY m.registration_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('name','%'.$name.'%', \PDO::PARAM_STR);

        $stmt->execute();

        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }

    /**
     * @return Member[] of member entities.
     */
    public function getNewMembers()
    {
        $sql = "SELECT
                m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.registration_date,
                m.profile_picture
                FROM member m
                ORDER BY m.registration_date DESC";
        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }

    /**
     * @param Interest[] $interests
     * @return Member[] of member entities.
     */
    public function getNewMembersWithInterests($interests)
    {
        $interests_group = [];
        foreach ($interests as $interest) {
            $interests_group[] = $interest->getName();
        }
        $in = implode(',', array_fill(0, count($interests_group), '?'));

        $sql = "SELECT DISTINCT
                m.member_id,
                m.username,
                m.first_name,
                m.last_name,
                m.registration_date,
                m.profile_picture
                FROM has_interests i
                JOIN member m ON i.member_id=m.member_id
                WHERE i.interest_name IN ($in)
                ORDER BY m.registration_date DESC";
        $stmt = $this->db->prepare($sql);

        foreach ($interests_group as $k => $id)
            $stmt->bindValue(($k+1), $id);

        $stmt->execute();

        $results = $stmt->fetchAll();
        return array_map(function ($row) {
            return new Member($row);
        },$results);
    }

    /**
     * @param $member_id int|string
     * @param $hashed_pwd string The hashed password
     * @return bool
     */
    public function updatePassword($member_id, $hashed_pwd)
    {
        $stmt = $this->db->prepare('UPDATE member SET password = :pwd WHERE member_id = :member_id');
        $stmt->bindValue(':member_id', $member_id);
        $stmt->bindValue(':pwd', $hashed_pwd);
        return $stmt->execute();
    }
}
