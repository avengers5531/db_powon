<?php

namespace Powon\Services;
/**
 * Class InvoiceService
 * @package Powon\services
 */
use Powon\Entity\Invoice;
use Powon\Entity\Member;


interface InvoiceService
{
    /**
     * @param int $invoice_id
     * @return Invoice
     */
    public function getInvoiceById($invoice_id);

    /**
     * @param int $member_id
     * @return Invoice[] of invoice entities.
     */
    public function getInvoiceByMember($member_id);

    /**
     * @return Invoice[] of invoice entities.
     */
    public function getUnpaidInvoices();

    /**
     * Sets the billing period to consider for the invoices
     * @param $period int in months
     */
    public function setSubscriptionPeriod($period);

    /**
     * @return \DateInterval
     */
    public function getSubscriptionPeriod();

    /**
     * Sets the grace period after the member anniversary date where the member can use the site.
     * Also marks the deadline of a payment after the billing period start date
     * @param $period int in days
     */
    public function setGracePeriod($period);

    /**
     * @return \DateInterval
     */
    public function getGracePeriod();

    /**
     * Sets the amount due for a subscription
     * @param $fee float
     */
    public function setSubscriptionFee($fee);

    /**
     * Gets the invoice for the given member.
     * If there is no invoice, it will generate one.
     * @param $member Member
     * @param null $base_date \DateTime take base_date as the reference period (defaults to now)
     * @return Invoice
     */
    public function getInvoiceFromDate($member, $base_date = null);


}
