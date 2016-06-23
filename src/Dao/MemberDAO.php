<?php

namespace Powon\Dao;

interface MemberDAO {

    /**
     * @return array of member entities.
     */
    public function getAllMembers();
}