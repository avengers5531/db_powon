<?php

namespace Powon\Services\Implementation;

use \Powon\Services\MemberService as MemberService;
use \Powon\Dao\MemberDAO as MemberDAO;

class MemberServiceImpl implements MemberService
{
    /**
     * @var MemberDAO
     */
    private $memberDAO;
    
    public function __construct(MemberDAO $dao)
    {
        $this->memberDAO = $dao;
    }
    
    public function getAllMembers() {
        return $this->memberDAO->getAllMembers();
    }
}