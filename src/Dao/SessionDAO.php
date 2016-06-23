<?php

namespace Powon\Dao;


interface SessionDAO
{
    public function getSessionEntity($token);
    
    public function updateSessionEntity();
}