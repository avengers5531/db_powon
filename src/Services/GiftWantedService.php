<?php
namespace Powon\Services;

use Powon\Entity\GiftWanted;
use Powon\Entity\Member;

interface GiftWantedService {

    const FIELD_GIFT = 'gift_name';
    /**
     * @param $member_id
     * @return array of GiftWanted entities
     */
    public function getWishListById($member_id);

    /**
     * @param $from_member Member
     * @param $to_member Member
     * @param $gift_name string
     * @return bool
     */
    public function giveGift($from_member, $to_member, $gift_name);

    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function requestGift($member_id, $gift_name);

    /**
     * @param $member_id string|int The member id whose wish list needs to be updated
     * @param $gifts array of strings
     * @return bool
     */
    public function updateWishList($member_id, $gifts);

    /**
     * Gets the gift inventory
     * @return [string]
     */
    public function getGiftInventory();
}
