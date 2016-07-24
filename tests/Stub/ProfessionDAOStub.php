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
use Powon\Entity\WorkAs;

class ProfessionDAOStub implements ProfessionDAO
{
   
    public $profession_name;

    /**
     * @var array
     */
    public $works_as;
    
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

    /**
     * @return [Profession]
     */
    public function getAllProfessions()
    {
        $results = [];
        foreach ($this->profession_name as &$profession_name) {
            $results[] = new Profession($profession_name);
        }
        return $results;
    }

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return [WorkAs]
     */
    public function getProfessionForMember($id)
    {
        $results = [];
        if(count($this->works_as) > 0){
            foreach ($this->works_as as &$row) {
                if ($row['member_id'] === $id) {
                    $results = new WorkAs($row);
                }
            }
        }
        return $results;
    }

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return bool true on success, failure otherwise
     */
    public function updateProfessionForMember($workAs){
        if (count($this->getProfessionForMember($workAs->getMemberId())) > 0) {
           foreach ($this->works_as as &$row) {
                if ($row['member_id'] === $workAs->getMemberId()) {
                    $row['profession_name'] = $workAs->getName();
                    $row['date_started'] = $workAs->getDateStarted();
                    $row['date_ended'] = $workAs->getDateEnded();
                }
            }
        } else {
            $this->works_as[] = ['profession_name' => $workAs->getName(),
                'member_id' => $workAs->getMemberId(), 'date_started' => $workAs->getDateStarted(),'date_ended' => $workAs->getDateEnded()
            ];
        }
    }
}
