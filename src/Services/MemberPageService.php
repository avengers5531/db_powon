<?php

namespace Powon\Services;

use Powon\Entity\MemberPage;

interface MemberPageService {
  /**
   * @param int id
   * @return a MemberPage Entity
   */
   public function getMemberPageByPageId($id);

  /**
   * @param int id
   * @return a MemberPage Entity
   */
   public function getMemberPageByMemberId($id);
}
