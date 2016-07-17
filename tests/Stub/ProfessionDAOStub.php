<?php
/**
 * Created by IntelliJ IDEA.
 * User: link
 * Date: 2016-07-10
 * Time: 1:28 PM
 */

namespace Powon\Test\Stub;


use Powon\Dao\ProfessionDAO;
use Powon\Entity\Profession;

class ProfessionDAOStub implements ProfessionDAO
{
   
    public $profession_name;
    
    public function getProfessionByName($name)
    {
        foreach ($this->profession_name as &$profession_name) {
            if ($profession_name['profession_name'] === $name) {
               return new Profession($profession_name);
            }
        }
        return null;
    }
     public function createProfession($profession)
    {
         $this->profession_name[] = $profession->toObj();
        return true;
    }

    public function RemoveProfession($profession)
    {
        return true;
    }
}
