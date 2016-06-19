<?php
require_once __DIR__.'/../entities/MemberEntity.php';

interface MemberDAO {

    /**
     * @return array of member entities.
     */
    public function getAllMembers();
}