<?php

namespace Powon\Dao;

use Powon\Entity\MemberPage;

interface MemberPageDAO{
  /**
   * @param int $id
   * @return MemberPage|null
   */
  public function getMemberPageByPageId($id);

  /**
   * @param int $id
   * @return MemberPage|null
   */
  public function getMemberPageByMemberId($id);
  
  //
  // /**
  //  * @param string $username
  //  * @return Page|null
  //  */
  // public function getMemberPageByByUsername($username);

}
