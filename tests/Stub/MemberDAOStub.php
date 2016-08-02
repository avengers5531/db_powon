<?php

namespace Powon\Test\Stub;

use Powon\Dao\MemberDAO;
use Powon\Entity\Member;

class MemberDaoStub implements MemberDAO {

    /**
     * @var array of mock member data.
     */
    public $members;

    public function __construct()
    {
        $this->members = [];
    }

    public function getAllMembers()
    {
        $results = $this->members;
        return array_map(function($data) {
            return new Member($data);
        }, $results);
    }

    /**
     * @param int $id
     * @return Member|null
     */
    public function getMemberById($id)
    {
        for ($i = 0; $i < count($this->members); $i++) {
            if ($this->members[$i]['member_id'] == $id) {
                return new Member($this->members[$i]);
            }
        }
        return null;
    }

    /**
     * @param string $username
     * @param bool $withPwd Set to true if you want the hashed password of the user.
     * @return Member|null
     */
    public function getMemberByUsername($username, $withPwd = false)
    {
        for ($i = 0; $i < count($this->members); $i++) {
            if (strcmp($this->members[$i]['username'],$username) === 0) {
                return new Member($this->members[$i]);
            }
        }
        return null;
    }

    /**
     * @param string $email
     * @param bool $withPwd set to true if you want the hashed password.
     * @return Member|null
     */
    public function getMemberByEmail($email, $withPwd = false)
    {
        for ($i = 0; $i < count($this->members); $i++) {
            if (strcmp($this->members[$i]['user_email'],$email) === 0) {
                return new Member($this->members[$i]);
            }
        }
        return null;
    }

    /**
     * @param $entity Member
     * @param $hashed_pwd string
     * @return bool
     */
    public function createNewMember($entity, $hashed_pwd)
    {
        $new_member = $entity->toObject();
        $new_member['password'] = $hashed_pwd;
        $this->members[] = $new_member;
        return true;
    }

    /**
     * @param $member_id Member : the Member entity with updated values
     * @return bool : true if update successful
     */
    public function updateMember($member)
    {
        for ($i = 0; $i < count($this->members); $i++) {
            if ($this->members[$i]['member_id'] == $member->getMemberId()) {
                $this->members[$i]['user_email'] = $member->getUserEmail();
                $this->members[$i]['first_name'] = $member->getFirstName();
                $this->members[$i]['last_name'] = $member->getLastName();
                $this->members[$i]['date_of_birth'] = $member->getDateOfBirth();
                return true;
            }
        }
        return false;
    }

    public function getNewMembersWithInterests($interests)
    {
        $results = [];
        foreach ($this->members as $member) {
            foreach ($interests as $interest) {
                $flag = false;
                foreach ($member['has_interests'] as $member_interest) {
                    if(strcmp($member_interest['interest_name'],$interest->getName()) === 0){
                        $results[] = $member;
                        $flag = true;
                        break;
                    }
                }
                if($flag){
                    break;
                }
            }
        }
        return $results;
    }

    public function searchMembersByNameWithInterests($name,$interests)
    {
        return [];
    }

    public function searchMembersByName($name)
    {
        return [];
    }

    public function getNewMembers()
    {
        return [];
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteMember($id)
    {
        $this->members = array_filter($this->members, function ($it)
        use ($id)
        {
           return $it['member_id'] != $id;
        });
    }
}
