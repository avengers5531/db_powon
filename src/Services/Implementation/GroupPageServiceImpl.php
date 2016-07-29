<?php

namespace Powon\Services\Implementation;


use Powon\Dao\GroupPageDAO;
use Powon\Entity\GroupPage;
use Powon\Services\GroupPageService;
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
     * @param $requestParams array: Contains the post request parameters.
     * @return array
     * Returns an array of the following form:
     * ['success' => bool, 'message' => string, 'page_id' => int (The id of the newly created page if successful)]
     */
    public function createGroupPage($page_owner, $group_id, $requestParams)
    {
        // TODO: Implement createGroupPage() method.
    }

    /**
     * Updates the title and description of the page. Changing owner is not supported.
     * @param $page_id int
     * @param $requestParams array
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateGroupPage($page_id, $requestParams)
    {
        if(empty($paramsRequest[GroupPageService::FIELD_PAGE_DESCRIPTION]) && !empty($paramsRequest[GroupPageService::FIELD_PAGE_TITLE])) {
            $this->groupPageDao->updateGroupPageTitle($page_id, $paramsRequest[GroupPageService::FIELD_PAGE_TITLE]);
            return true;
        }
        elseif(empty($paramsRequest[GroupPageService::FIELD_PAGE_TITLE]) && !empty($paramsRequest[GroupPageService::FIELD_PAGE_DESCRIPTION])) {
            $this->groupPageDao->updateGroupPageDescription($page_id, $paramsRequest[GroupPageService::FIELD_PAGE_DESCRIPTION]);
            return true;
        }
        elseif(!empty($paramsRequest[GroupPageService::FIELD_PAGE_TITLE]) && !empty($paramsRequest[GroupPageService::FIELD_PAGE_DESCRIPTION])) {
            $this->groupPageDao->updateGroupPageTitle($page_id, $paramsRequest[GroupPageService::FIELD_PAGE_TITLE]);
            $this->groupPageDao->updateGroupPageDescription($page_id, $paramsRequest[GroupPageService::FIELD_PAGE_DESCRIPTION]);
            return true;
        }
        else{
            return false;
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
            return $this->groupPageDao->getGroupPagesForMember($group_id, $member_id);
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
     * @param $page_id int
     * @param $group_id
     * @param $access_type string (either GroupPage::ACCESS_EVERYONE or GroupPage::ACCESS_PRIVATE)
     * @param $requestParams array - array with member id's
     * This array contains the list of member_id as keys. i.e,
     * iterates through the keys of the array to get all the member ids to add to the member_can_access_page table if
     * access_type is private.
     * example: foreach($requestParams as $member_id => $value) {
     *     //do stuff with $member_id and ignore $value
     * }
     * @return array ['success' => bool, 'message' => string]
     */
    public function updatePageAccess($page_id, $group_id, $access_type, $requestParams)
    {
        try{
            if($this->groupPageDao->deleteGroupPage($page_id)){
             }
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred when deleting group page: ". $ex->getMessage());
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
            return $this->groupPageDao->getGroupPagesForMember($page_id, $group_id);
        } catch(\PDOException $ex){
            $this->log->error("A pdo exception: ". $ex->getMessage());
            return [];
        }
    }
}
