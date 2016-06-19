<?php

require_once __DIR__.'/../../dao/MemberDAO.php';
require_once __DIR__.'/../MemberService.php';

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