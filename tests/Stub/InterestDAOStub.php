<?php
/**
 * Created by IntelliJ IDEA.
 * User: link
 * Date: 2016-07-10
 * Time: 1:28 PM
 */

namespace Powon\Test\Stub;


use Powon\Dao\InterestDAO;
use Powon\Entity\Interest;

class InterestDAOStub implements InterestDAO
{
    /**
     * @var array
     */
    public $interests;

    /**
     * @var array
     */
    public $has_interests;

    /**
     * Gets the interest entity associated with the name
     * @param $name string The interest name
     * @return Interest|null
     */
    public function getInterestByName($name)
    {
        foreach ($this->interests as &$interest) {
            if ($interest['interest_name'] === $name) {
               return new Interest($interest); 
            }
        }
        return null;
    }

    /**
     * Gets the list of interests for the given member id.
     * @param $id int the member id
     * @return [Interest]
     */
    public function getInterestsForMember($id)
    {
        $results = [];
        foreach ($this->has_interests as &$row) {
            if ($row['member_id'] === $id) {
                $results[] = new Interest($row);
            }
        }
        return $results;
    }

    /**
     * @param $interest Interest
     * @return bool true on success, otherwise false
     */
    public function createInterest($interest)
    {
        $this->interests[] = $interest->toObj();
        return true;
    }

    /**
     * @param $interest Interest
     * @param $member int The member id
     * @return bool true on success, failure otherwise
     */
    public function addInterestForMember($interest, $member)
    {
        $this->has_interests[] = ['interest_name' => $interest->getName(),
            'member_id' => $member
        ];
    }
}
