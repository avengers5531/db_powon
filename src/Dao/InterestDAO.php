<?php

namespace Powon\Dao;

use Powon\Entity\Interest;

interface InterestDAO {

    /**
     * Gets the interest entity associated with the name 
     * @param $name string The interest name
     * @return Interest|null
     */
    public function getInterestByName($name);

    /**
     * Gets the list of interests for the given member id.
     * @param $id int the member id
     * @return [Interest]
     */
    public function getInterestsForMember($id);

    /**
     * @param $interest Interest
     * @return bool true on success, otherwise false
     */
    public function createInterest($interest);

    /**
     * @param $interest Interest 
     * @param $member int The member id
     * @return bool true on success, failure otherwise
     */
    public function addInterestForMember($interest, $member);

    /*public function RemoveInterestByNam($name);*/

    public function RemoveInterestByNamForMamber($name, $member);

    /**
     * @return [Interest]
     */
    public function getAllInterests();
}
