<?php

namespace Powon\Dao;

use Powon\Entity\GiftWanted;

/**
 * Interface GiftWantedDAO
 * @package Powon\Dao
 */
interface GiftWantedDAO {
    /**
     * @param $member_id
     * @return array of GiftWanted entities
     */
    public function getWishListById($member_id);

    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function giveGift($member_id, $gift_name);

    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function requestGift($member_id, $gift_name);

    /**
     * @param $gift_name
     * @return bool
     */
    public function verifyGiftExists($gift_name);

    /**
     * List of gifts in the inventory
     * @return [string]
     */
    public function getGiftList();

    /**
     * Removes the gifts from the wish list.
     * @param $member_id int
     * @return bool
     */
    public function removeGiftsForMember($member_id);

}
