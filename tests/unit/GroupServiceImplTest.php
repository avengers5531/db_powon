<?php


use PHPUnit\Framework\TestCase;
use Powon\Services\GroupService;
use Powon\Test\Stub\LoggerStub;
use Powon\Test\Stub\GroupDaoStub;

class GroupServiceImplTest extends TestCase
{

    /**
     * @var GroupService $groupService
     */
    private $groupService;

    public function setUp(){
        parent::setUp();
        $dao = new GroupDaoStub();
        $dao->groups = array(
            [
                'powon_group_id' => 1,
                'group_title' => 'Group1',
                'description' => 'First group',
                'group_picture' => '',
                'group_owner' => 1
            ],
            [
                'powon_group_id' => 2,
                'group_title' => 'Group2',
                'description' => 'Second group',
                'group_picture' => '',
                'group_owner' => 1
            ]
        );
        $dao2 = new GroupDaoStub();
        $dao2->isGroupMember = array(
          [
              'powon_group_id' => 1,
              'member_id' => 1
          ],
          [
              'powon_group_id' => 2,
              'member_id' => 2
          ]
        );
        $logger = new LoggerStub();
        $this->groupService = new \Powon\Services\Implementation\GroupServiceImpl($logger,$dao, $dao2);
    }

    public function memberBelongsToGroup(){
        $groups = $this->groupService->memberBelongsToGroup(1 , 1);
        $this->assertEquals($groups->getGroupId(), 1);
        $this->assertNotEquals($groups->getGroupId(), 2);
    }

    public function testGetGroupsMemberBelongsTo(){
        $groups = $this->groupService->getGroupsMemberBelongsTo(1);
        $this->assertEquals($groups->getGroupId(), 1);
        $this->assertEquals($groups->getGroupTitle(), 'Group1');
        $this->assertEquals($groups->getDescription(), 'First group');
        $this->assertEquals($groups->getGroupPicture(), '');
        $this->assertEquals($groups->getGroupOwner(), 1);
    }

    public function testGetGroupsMemberDoesNotBelongTo(){
        $groups = $this->groupService->getGroupsMemberDoesNotBelongTo(1);
        
    }



}