<?php

namespace Powon\Dao\Implementation;

use Powon\Dao\MemberPageDAO as MemberPageDAO;
use Powon\Entity\MemberPage as MemberPage;

class MemberPageDAOImpl implements MemberPageDAO {
  private $db;

  /**
   * MemberPageDaoImpl constructor.
   * @param \PDO $pdo
   */
  public function __construct($pdo)
  {
      $this->db = $pdo;
  }

  /**
   * @param int $id
   * @return MemberPage|null
   */
  public function getMemberPageByPageId($id){
    $sql = 'SELECT page.page_id,
            page.date_created,
            page.page_title,
            profile_page.member_id,
            profile_page.page_access
            FROM page NATURAL JOIN profile_page
            WHERE page.page_id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    if ($stmt->execute()) {
        $row = $stmt->fetch();
        return ($row ? new MemberPage($row) : null);
    } else {
        return null;
    }
  }

  /**
   * @param int $id
   * @return MemberPage|null
   */
  public function getMemberPageByMemberId($id){
    $sql = 'SELECT page.page_id,
            page.date_created,
            page.page_title,
            profile_page.member_id,
            profile_page.page_access
            FROM page NATURAL JOIN profile_page
            WHERE member_id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    if ($stmt->execute()) {
        $row = $stmt->fetch();
        return ($row ? new MemberPage($row) : null);
    } else {
        return null;
    }
  }

  // /**
  //  * @param string $username
  //  * @return Page|null
  //  */
  // // public function getMemberPageByByUsername($username);

}
