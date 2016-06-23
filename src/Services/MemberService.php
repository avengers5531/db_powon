<?php

namespace Powon\Services;

interface MemberService {

    /**
     * @return array<MemberEntity> All the members
     */
    public function getAllMembers();
}
