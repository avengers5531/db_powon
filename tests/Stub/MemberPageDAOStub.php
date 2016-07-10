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
     * @return Member|null
     */
    public function getMemberPageByPageId($id)
    {
      // $crap = array(
      //         'page_id' => 1,
      //         'date_created' => '2015-02-03',
      //         'title' => 'User1Page',
      //         'member_id' => 1,
      //         'page_access' => 12,
      //       );
      // return new MemberPage($crap);

        for ($i = 0; $i < count($this->member_pages); $i++) {
            if ($this->member_pages[$i]['page_id'] == $id) {
                return new MemberPage($this->member_pages[$i]);
            }
        }
        return null;
    }
}
