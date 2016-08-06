<?php
namespace Powon\Entity;

class GiftWanted
{
    private $gift_name;
    private $member_id;
    private $date_received;


    public function __construct(array $data)
    {
        $this->gift_name = $data['gift_name'];
        $this->member_id = $data['member_id'];
        if (isset($data['date_received'])) {
            $this->date_received = $data['date_received'];
        }
    }

    /**
     * @return int
     */
    public function getMemberId() {
        return $this->member_id;
    }

    /**
     * @return string
     */
    public function getGiftName() {
        return $this->gift_name;
    }

    /**
     * @return string
     */
    public function getDateReceived() {
        return $this->date_received;
    }

    /**
     * @param $member_id
     *
     */
    public function setMemberId($member_id) {
        $this->member_id = $member_id;
    }

    /**
     * @param $gift_name
     *
     */
    public function setGiftName($gift_name) {
        $this->gift_name = $gift_name;
    }

    /**
     * @return string
     */
    public function setDateReceived($date_received) {
        $this->date_received = $date_received;
    }

    
}
