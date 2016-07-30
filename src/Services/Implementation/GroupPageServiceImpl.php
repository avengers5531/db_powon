<?php

namespace Powon\Services\Implementation;


use Powon\Dao\GroupPageDAO;
use Powon\Entity\GroupPage;
use Powon\Services\GroupPageService;
use Powon\Utils\Validation;
use Psr\Log\LoggerInterface;

class GroupPageServiceImpl implements GroupPageService
{
    /**
     * @var GroupPageDAO
     */
    private $groupPageDao;

    /**
     * @var $log LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $logger, GroupPageDAO $groupPageDAO)
    {
        $this->log = $logger;
        $this->groupPageDao = $groupPageDAO;
    }

    /**
     * Creates a page within the given group. Also inserts the owner id in the member_has_access table
     * for this page.
     * @param $page_owner int the member_id of the page owner
     * @param $group_id int the group id
     * @param $requestParams array: Contains the post request parameters. Title, description and access_type
     * @return array
     * Returns an array of the following form:
     * ['success' => bool, 'message' => string, 'page_id' => int (The id of the newly created page if successful)]
     */
    public function createGroupPage($page_owner, $group_id, $requestParams)
    {
        $msg = '';
        if(!Validation::validateParametersExist(
            [GroupPageService::FIELD_PAGE_TITLE,
             GroupPageService::FIELD_PAGE_DESCRIPTION,
             GroupPageService::FIELD_ACCESS], $requestParams)){
            $msg = 'Invalid parameters entered.';
            $this->log->debug("Registration failed: $msg", $requestParams);
        }
        else{
 /*           $res = $this->doesGroupPageExist($requestParams[GroupPageService::FIELD_PAGE_TITLE],
                $requestParams[GroupPageService::FIELD_PAGE_DESCRIPTION]);
            if(!$res['success']){
                $msg = $res['message'];
                $this->log->debug("Creating new group page failed: $msg", $requestParams);
            }
 */
            $data = array(
                'page_title' => $requestParams[GroupPageService::FIELD_PAGE_TITLE],
                'page_description' => $requestParams[GroupPageService::FIELD_PAGE_DESCRIPTION],
                'access_type' => $requestParams[GroupPageService::FIELD_ACCESS],
                'page_owner' => $page_owner,
                'page_group' => $group_id
            );
            $newGroupPage = new GroupPage($data);
            try{
                $group_page_id = $this->groupPageDao->createGroupPage($newGroupPage);
                $this->log->info('Trying to create group page', $newGroupPage->toObject());
                if($group_page_id > 0){
                    $this->log->info('Created new group page',
                        [
                            'page_title' => $requestParams[GroupPageService::FIELD_PAGE_TITLE],
                            'page_description' => $requestParams[GroupPageService::FIELD_PAGE_DESCRIPTION],
                            'page owner id' => $page_owner
                        ]
                    );
                    $this->groupPageDao->addMemberToGroupPage($group_page_id, $page_owner, $group_id);
                    $this->log->info('Created group page');
                    return array('success' => true,
                                 'message' => "New group page created.",
                                 'page_id' => $group_page_id
                    );
                }
            } catch (\PDOException $ex) {
                $this->log->error("A pdo exception occurred when creating new group page: " . $ex->getMessage());
            }
        }
            return array(
                'success' => false,
                'message' => 'Could not create new group page'
            );
    }

    /**
     * Updates the title and description of the page. Changing owner is not supported.
     * @param $page_id int
     * @param $requestParams array
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateGroupPage($page_id, $requestParams)
    {
        $msg = ' ';
        if(!Validation::validateParametersExist(
            [GroupPageService::FIELD_PAGE_TITLE,
             GroupPageService::FIELD_PAGE_DESCRIPTION],$requestParams)){
            $msg = 'Invalid parameters entered.';
            $this->log->debug("Registration failed: $msg", $requestParams);
            return array(
                'success' => false,
                'message' => 'Could not updated group page title and/or title'
            );
        }
        else{
            $this->groupPageDao->updateGroupPageTitle($page_id, $requestParams[GroupPageService::FIELD_PAGE_TITLE]);
            $this->groupPageDao->updateGroupPageDescription($page_id, $requestParams[GroupPageService::FIELD_PAGE_DESCRIPTION]);
            return array(
                'success' => true,
                'message' => 'Updated group page title and description'
            );
        }
    }

    /**
     * @param $page_id int The id of the page to delete
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteGroupPage($page_id)
    {
        try{
            if($this->groupPageDao->deleteGroupPage($page_id)){
                return true;
            }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting group page: ". $ex->getMessage());
        }
        return false;
    }

    /**
     * Gets a GroupPage entity by page id.
     * @param $page_id int
     * @return GroupPage|null
     */
    public function getPageById($page_id)
    {
        try{
            return $this->groupPageDao->getGroupPageById($page_id);
        } catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return null;
        }
    }

    /**
     * Returns all the pages for the group.
     * This method should only be called by the group owner or admin.
     * @param $group_id int
     * @return GroupPage[]
     */
    public function getGroupPages($group_id)
    {
        try{
            return $this->groupPageDao->getPagesOfGroup($group_id);
        }catch (\PDOException $ex){
            $this->log->error("A pdo exception occurred: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * Returns the group pages for the given member (i.e. makes sure member has access to the pages)
     * @param $group_id int
     * @param $member_id int
     * @return  GroupPage[]
     */
    public function getGroupPagesForMember($group_id, $member_id)
    {
        try{
            $this->log->info("Get group pages for member " . $member_id . " in group " . $group_id);
            $pages = $this->groupPageDao->getGroupPagesForMember($member_id, $group_id);
            $this->log->debug('Pages retrieved...', array_map(function (GroupPage $page) {
                return $page->toObject();
            }, $pages));
            return $pages;
        } catch(\PDOException $ex){
            $this->log->error("A pdo exception: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * This method updates the page access (access_type). It deletes all the users associated with the given page id
     * from the member_can_access_page table EXCEPT the owner.
     * Then, IF the access is private, it adds the new members given in the array to the table. Otherwise
     * it ignores the 3rd parameter after setting the access_type to public.
     * @param $page_id int|string
     * @param $group_id int|string The group id in which the page is located.
     * @param $access_type string (either GroupPage::ACCESS_EVERYONE or GroupPage::ACCESS_PRIVATE)
     * @param $members array This array contains the list of member_id.
     * @param $page_owner
     * @return array ['success' => bool, 'message' => string]
     */
    public function updatePageAccess($page_id, $group_id, $access_type, $members, $page_owner){
        try{
            $this->groupPageDao->updateAccessType($page_id, $access_type);
            $this->groupPageDao->deleteGroupPageMembers($page_id, $page_owner);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting group page members: ". $ex->getMessage());
        }
        if(GroupPage::ACCESS_PRIVATE == $access_type){
            foreach ($members as $id){
                $this->groupPageDao->addMemberToGroupPage($page_id, $id, $group_id);
            }
            
            return array(
                'success' => true,
                'message' => 'Given private access to members'
            );
        }
        elseif(GroupPage::ACCESS_EVERYONE){
            $membersInGroup = $this->groupPageDao->getMembersWithPageAccess($page_id);
            foreach ($membersInGroup as $id){
                $this->groupPageDao->addMemberToGroupPage($page_id, $id, $group_id);
            }
            return array(
                'success' => true,
                'message' => 'Given private access to members'
            );
        }
        else{
            return array(
                'success' => false,
                'message' => 'Given public access to members'
            );
        }
    }

    /**
     * Gets a list of members who have access to the page
     * @param $page_id int|string The page id
     * @param $group_id int|string The group id
     * @return  [Member]
     */
    public function getMembersWithAccessToPage($page_id, $group_id)
    {
        try{
            return $this->groupPageDao->getMembersWithPageAccess($page_id);
        } catch(\PDOException $ex){
            $this->log->error("A pdo exception: ". $ex->getMessage());
            return [];
        }
    }

    /**
     * @param $page_title
     * @param $page_description
     * @return bool
     */
    public function doesGroupPageExist($page_title, $page_description)
    {
        $page = null;
        try{
            $page = $this->groupPageDao->getGroupPageByTitle($page_title);
            if($page){
                return [
                    'success' => true,
                    'message' => 'Found an existing group page.'
                ];
            }
        } catch (\PDOException $ex) {
            $this->log->error('A pdo exception occurred when checking if a group with' .
                " title $page_title exists: " . $ex->getMessage());
        }

        return [
            'success' => false,
            'message' => 'Did not find a group page with the given parameters'
        ];
    }
}
