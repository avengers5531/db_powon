<?php

use PHPUnit\Framework\TestCase;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\MemberPageDaoStub;


class MemberPageServiceImplTest extends TestCase
{
    /**
     * @var \Powon\Services\MemberService $memberService
     */
    private $memberPageService;

    public function setUp()
    {
        parent::setUp();
        $dao = new MemberPageDaoStub();
        $dao->member_pages = array(
            [
                'page_id' => 1,
                'date_created' => '2015-02-03',
                'page_title' => 'User1Page',
                'member_id' => 1,
                'page_access' => 12,
            ],
            [
              'page_id' => 2,
              'date_created' => '2016-06-10',
              'page_title' => 'User3Page',
              'member_id' => 3,
              'page_access' => 10,
            ]);
        $logger = new LoggerStub();
        $this->memberPageService = new \Powon\Services\Implementation\MemberPageServiceImpl($logger,$dao);
    }

    public function testGetMemberPageById(){
      $res = $this->memberPageService->getMemberPageByPageId(1);
      $this->assertNotEquals($res, null);
      $this->assertEquals($res->getPageId(), 1);
      $this->assertEquals($res->title(), 'User1Page');
    }

    public function testGetMemberMemberById(){
      $res = $this->memberPageService->getMemberPageByMemberId(3);
      $this->assertNotEquals($res, null);
      $this->assertEquals($res->getPageId(), 2);
      $this->assertEquals($res->title(), 'User3Page');
    }

}
