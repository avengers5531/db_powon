<?php

namespace Powon\Services\Implementation;

use Powon\Dao\InvoiceDAO;
use Powon\Dao\MemberDAO;
use Powon\Entity\Invoice;
use Powon\Entity\Member;
use Powon\Services\InvoiceService;
use Powon\Utils\DateTimeHelper;
use Psr\Log\LoggerInterface;


/**
 * Class InvoiceServiceImpl
 * The basic implementation of the invoice service
 * @package Powon\Services\Implementation
 */

class InvoiceServiceImpl implements InvoiceService
{

    private $subscription_period;
    private $grace_period;
    private $amount_due;

    /**
     * @var InvoiceDAO
     */
    private $invoiceDAO;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var MemberDAO
     */
    private $memberDAO;

    public function __construct(LoggerInterface $logger, InvoiceDAO $dao, MemberDAO $memberDAO)
    {
        $this->invoiceDAO = $dao;
        $this->log = $logger;
        $this->memberDAO = $memberDAO;
        // default values for the grace period, amount due and subscription period:
        $this->subscription_period = new \DateInterval("P12M");
        $this->grace_period = new \DateInterval("P30D");
        $this->amount_due = 32.00;
    }

    /**
     * @param int $invoice_id
     * @return Invoice|null
     */
    public function getInvoiceById($invoice_id)
    {
        try {
            return $this->invoiceDAO->getInvoiceById($invoice_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return null;
        }
    }

    /**
     * @param int $member_id
     * @return Invoice[] of invoice entities.
     */

    public function getInvoiceByMember($member_id)
    {
        try {
            return $this->invoiceDAO->getInvoiceByMember($member_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
    }

    /**
     * @return Invoice[] of invoice entities.
     */

    public function getUnpaidInvoices()
    {
        try {
            return $this->invoiceDAO->getUnpaidInvoices();
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
    }

    /**
     * @param $invoice_id
     * @param $member
     */
    public function payInvoice($invoice_id, $member)
    {
        try {
            $result = $this->invoiceDAO->payInvoice((int)$invoice_id, $member);

        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }

        $invoice = $this->getInvoiceById($invoice_id);
        $currentDate = DateTimeHelper::fromString($invoice->getDatePaid());
        $invoiceEnd = DateTimeHelper::fromString($invoice->getBillingEnd());
        if (($invoiceEnd->getTimeStamp() - $currentDate->getTimeStamp()) > 0)
            $this->memberService->activateStatus($member);
        return $result;
    }
    /**
     * Sets the billing period to consider for the invoices
     * @param $period int in months
     */
    public function setSubscriptionPeriod($period)
    {
        $this->subscription_period = new \DateInterval("P$period"."M");
    }

    /**
     * Sets the grace period after the member anniversary date where the member can use the site.
     * Also marks the deadline of a payment after the billing period start date
     * @param $period int in days
     */
    public function setGracePeriod($period)
    {
        $this->grace_period = new \DateInterval("P$period"."D");
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
        if (!$base_date)
            $base_date = new \DateTime(); // now
        try {
            $search_start_date = clone $base_date;
            // now minus subscription period interval
            $search_start_date->sub($this->subscription_period);
            $invoice = $this->invoiceDAO
                ->getInvoiceForMemberWithBillingStartDateBetween($member->getMemberId(),
                    DateTimeHelper::toString($search_start_date),
                    DateTimeHelper::toString($base_date));
            if ($invoice) {
                $this->log->debug("Found invoice for member ". $member->getMemberId());
                return $invoice;
            }
            $this->log->info("No invoice found for the current period for member ". $member->getMemberId().
                " Creating one now...");
            $memberRegDateStr = $member->getRegistrationDate();
            // current invoice
            $memberRegDate = DateTimeHelper::fromString($memberRegDateStr);
            $invoice_start = clone $memberRegDate;
            $subscription_period_in_seconds = DateTimeHelper::dateIntervalToSeconds($this->subscription_period);
            $n = floor(($base_date->getTimestamp() - $memberRegDate->getTimestamp()) / $subscription_period_in_seconds);

            $seconds_after_anniversary_date = $n * $subscription_period_in_seconds;
            $invoice_start->add(new \DateInterval("PT$seconds_after_anniversary_date" . "S"));


            $invoice_end = clone $invoice_start;
            $invoice_end->add($this->subscription_period);

            $invoice_deadline = clone $invoice_start;
            $invoice_deadline->add($this->grace_period);
            $data = [
                'amount_due' => $this->amount_due,
                'payment_deadline' => DateTimeHelper::toString($invoice_deadline),
                'billing_period_start' => DateTimeHelper::toString($invoice_start),
                'billing_period_end' => DateTimeHelper::toString($invoice_end),
                'account_holder' => $member->getMemberId()
            ];
            $invoice = new Invoice($data);
            $id = $this->invoiceDAO->createInvoice($invoice);
            if ($id > 0) {
                $invoice->setInvoiceId($id);
                return $invoice;
            } else {
                $this->log->error('Invoice was not able to generate', $invoice->toObject());
            }
        } catch (\PDOException $ex) {
            $this->log->error("PDO failed while trying to get/generate invoice for member " . $member->getMemberId().
            ": ". $ex->getMessage());
        } catch (\InvalidArgumentException $iaex) {
            $this->log->error("Invalid argument when trying to get invoice for the current period. ". $iaex->getMessage());
        }
        return null;
    }

    /**
     * Sets the amount due for a subscription
     * @param $fee float
     */
    public function setSubscriptionFee($fee)
    {
       $this->amount_due = $fee;
    }

    /**
     * @return \DateInterval
     */
    public function getSubscriptionPeriod()
    {
        return $this->subscription_period;
    }

    /**
     * @return \DateInterval
     */
    public function getGracePeriod()
    {
        return $this->grace_period;
    }
}

