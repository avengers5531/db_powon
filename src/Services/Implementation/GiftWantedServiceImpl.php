<?php

namespace Powon\Services\Implementation;

use Powon\Dao\GiftWantedDAO;
use Powon\Entity\GiftWanted;
use Powon\Entity\Member;
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


    public function __construct(LoggerInterface $log, GiftWantedDAO $dao)
    {
        $this->log = $log;
        $this->giftWantedDAO = $dao;
    }

    /**
     * @param $member_id
     * @return array of GiftWanted entities
     */
    public function getWishListById($member_id){
        $this->log->debug("get id from inside wishlistby id function: $member_id");
        try {
            return $this->giftWantedDAO->getWishListById($member_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
    }

    /**
     * @param $from_member Member
     * @param $to_member Member
     * @param $gift_name string
     * @return bool
     */
    public function giveGift($from_member, $to_member, $gift_name) {

        try {
            return $this->giftWantedDAO->giveGift($to_member->getMemberId(), $gift_name);
            // TODO send a message
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

    /**
     * Gets the gift inventory
     * @return [string]
     */
    public function getGiftInventory()
    {
        try {
            return $this->giftWantedDAO->getGiftList();
        } catch (\PDOException $ex) {
            $this->log->error("Error while fetching gift list: ". $ex->getMessage());
        }
        return [];
    }

    /**
     * @param $member_id string|int The member id whose wish list needs to be updated
     * @param $gifts array of strings
     * @return bool
     */
    public function updateWishList($member_id, $gifts)
    {
       try {
           if ($this->giftWantedDAO->removeGiftsForMember($member_id)) {
               foreach ($gifts as &$gift) {
                   // ignore return value
                   $this->requestGift($member_id, $gift);
               }
               return true;
           }

       } catch (\PDOException $ex) {
           $this->log->error("Error while updating member $member_id's wish list. " . $ex->getMessage());
       }
       return false;
    }
}
