<?php
namespace Powon\Services;

use Powon\Entity\GiftWanted;
use Powon\Entity\Member;

interface GiftWantedService {

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
}