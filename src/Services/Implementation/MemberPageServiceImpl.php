<?php

namespace Powon\Services\Implementation;

use Powon\Entity\MemberPage;
use Psr\Log\LoggerInterface;
use Powon\Services\MemberPageService;
use Powon\Dao\MemberPageDAO;

class MemberPageServiceImpl implements MemberPageService
{
  /**
   * @var MemberPageDAO
   */
  private $memberPageDAO;

  /**
   * @var LoggerInterface
   */
  private $log;

  public function __construct(LoggerInterface $logger, MemberPageDAO $dao)
  {
      $this->memberPageDAO = $dao;
      $this->log = $logger;
  }
  /**
   * @param id
   * @return MemberPage Entity
   */
   public function getMemberPageByPageId($id){
     try {
         return $this->memberPageDAO->getMemberPageByPageId($id);
     } catch (\PDOException $ex) {
         $this->log->error("A pdo exception occurred: ". $ex->getMessage());
         return null;
     }
   }

   public function getMemberPageByMemberId($id){
     try {
         return $this->memberPageDAO->getMemberPageByMemberId($id);
     } catch (\PDOException $ex) {
         $this->log->error("A pdo exception occurred: ". $ex->getMessage());
         return null;
     }
   }
}
