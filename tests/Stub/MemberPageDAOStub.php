<?php
namespace Powon\Test\Stub;

use Powon\Dao\MemberPageDAO;
use Powon\Entity\MemberPage;

class MemberPageDaoStub implements MemberPageDAO {

    /**
     * @var array of mock member data.
     */
    public $member_pages;

    public function __construct()
    {
        $this->member_pages = [];
    }


    /**
     * @param int $id
     * @return MemberPage|null
     */
    public function getMemberPageByPageId($id)
    {
        for ($i = 0; $i < count($this->member_pages); $i++) {
            if ($this->member_pages[$i]['page_id'] == $id) {
                return new MemberPage($this->member_pages[$i]);
            }
        }
        return null;
    }

    /**
     * @param int $id
     * @return MemberPage|null
     */
    public function getMemberPageByMemberId($id)
    {
        for ($i = 0; $i < count($this->member_pages); $i++) {
            if ($this->member_pages[$i]['member_id'] == $id) {
                return new MemberPage($this->member_pages[$i]);
            }
        }
        return null;
    }
}
