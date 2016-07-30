<?php

namespace Powon\Dao;


use Powon\Entity\Profession;

interface ProfessionDAO
{

    /**
     * Gets the interest entity associated with the name 
     * @param $name string The interest name
     * @return Interest|null
     */
    public function getProfessionByName($profession_name);

    /**
     * @param $interest Interest
     * @return bool true on success, otherwise false
     */
    public function createProfession($profession);

    /**
     * @param $interest Interest 
     * @param $member int The member id
     * @return bool true on success, failure otherwise
     */
    public function RemoveProfession($profession_name);

    /**
     * Gets the list of profession for the given member id.
     * @param $id int the member id
     * @return [Interest]
     */
    public function getProfessionForMember($id);

    /**
     * update profession for the given member id.
     * @param $id int the member id
     * @return bool true on success, failure otherwise
     */
    public function updateProfessionForMember($workAs);

    /**
     * @return [Profession]
     */
    public function getAllProfessions();

}
