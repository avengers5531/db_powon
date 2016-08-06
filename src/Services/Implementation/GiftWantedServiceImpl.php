<?php

namespace Powon\Services\Implementation;

use Powon\Dao\GiftWantedDAO;
use Powon\Entity\GiftWanted;
use Psr\Log\LoggerInterface;
use Powon\Services\GiftWantedService;


class GiftWantedServiceImpl implements GiftWantedService
{

    /**
     * @var GiftWantedDAO
     */
    private $giftWantedDAO;

    /**
     * @var LoggerInterface
     */
    private $log;


    /**
     * @param $member_id
     * @return array of GiftWanted entities
     */
    public function getWishListById($member_id){
        $this->log->info("get id from inside wishlistbyid function: $member_id");
        try {
            return $this->giftWantedDAO->getWishListById($member_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return false;
        }
    }

    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function giveGift($member_id, $gift_name){
        try {
                return $this->giftWantedDAO->giveGift($member_id, $gift_name);
            } catch (\PDOException $ex) {
                $this->log->error("A pdo exception occurred: " . $ex->getMessage());
                return false;
            }
    }

    /**
     * @param $member_id
     * @param $gift_name
     * @return bool
     */
    public function requestGift($member_id, $gift_name)
    {
        try {
            $giftExists = $this->giftWantedDAO->verifyGiftExists($gift_name);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return false;
        }
        if ($giftExists) {
            try {
                return $this->giftWantedDAO->requestGift($member_id, $gift_name);
            } catch (\PDOException $ex) {
                $this->log->error("A pdo exception occurred: " . $ex->getMessage());
                return false;
            }
        } else {
            $this->log->error("Requested gift doesn't exist in the database");
            return false;
        }
    }

}