<?php

namespace Powon\Test\Stub;


use Powon\Entity\Invoice;
use Powon\Entity\Member;
use Powon\Services\InvoiceService;

class InvoiceServiceMock implements InvoiceService
{

    /**
     * @param int $invoice_id
     * @return Invoice
     */
    public function getInvoiceById($invoice_id)
    {
        // TODO: Implement getInvoiceById() method.
        return null;
    }

    /**
     * @param int $member_id
     * @return Invoice[] of invoice entities.
     */
    public function getInvoiceByMember($member_id)
    {
        // TODO: Implement getInvoiceByMember() method.
        return [];
    }

    /**
     * @return Invoice[] of invoice entities.
     */
    public function getUnpaidInvoices()
    {
        // TODO: Implement getUnpaidInvoices() method.
        return [];
    }

    /**
     * Sets the billing period to consider for the invoices
     * @param $period int in months
     */
    public function setSubscriptionPeriod($period)
    {
        // TODO: Implement setSubscriptionPeriod() method.
    }

    /**
     * Sets the grace period after the member anniversary date where the member can use the site.
     * Also marks the deadline of a payment after the billing period start date
     * @param $period int in days
     */
    public function setGracePeriod($period)
    {
        // TODO: Implement setGracePeriod() method.
    }

    /**
     * Sets the amount due for a subscription
     * @param $fee float
     */
    public function setSubscriptionFee($fee)
    {
        // TODO: Implement setSubscriptionFee() method.
    }

    /**
     * Gets the invoice for the given member.
     * If there is no invoice, it will generate one.
     * @param $member Member
     * @param null $base_date \DateTime take base_date as the reference period (defaults to now)
     * @return Invoice
     */
    public function getInvoiceFromDate($member, $base_date = null)
    {
        // TODO: Implement getInvoiceFromDate() method.
        return null;
    }

    /**
     * @return \DateInterval
     */
    public function getSubscriptionPeriod()
    {
        return new \DateInterval("P12M");
    }

    /**
     * @return \DateInterval
     */
    public function getGracePeriod()
    {
        return new \DateInterval("P30D");
    }

    /**
     * @param $invoice_id
     * @param $member
     * @return bool
     */
    public function payInvoice($invoice_id, $member)
    {
        return true;
    }
}
