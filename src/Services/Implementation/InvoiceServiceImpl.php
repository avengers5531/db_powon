<?php

namespace Powon\Services\Implementation;

use Powon\Dao\InvoiceDAO;
use Powon\Dao\MemberDAO;
use Powon\Entity\Invoice;
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
    }

    /**
     * @param int $invoice_id
     * @return Invoice
     */
    public function getInvoiceById($invoice_id)
    {
        try {
            return $this->invoiceDAO->getInvoiceById($invoice_id);
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
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
    public function payInvoice($invoice_id, $member){
        try {
            $result = $this->invoiceDAO->payInvoice((int)$invoice_id, $member);

        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }

        $invoice = $this->getInvoiceById($invoice_id);
        $currentDate = DateTimeHelper::fromString($invoice->getDatePaid());
        $invoiceEnd = DateTimeHelper::fromString($invoice->getBillingEnd());
        if (($invoiceEnd->getTimeStamp() - $currentDate->getTimeStamp())  > 0)
        $this->memberService->activateStatus($member);
        return $result;
    }
}

