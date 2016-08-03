<?php

namespace Powon\Services\Implementation;

use Powon\Dao\InvoiceDAO;
use Powon\Entity\Invoice;

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

    public function __construct(LoggerInterface $logger, InvoiceDAO $dao)
    {
        $this->invoiceDAO = $dao;
        $this->log = $logger;
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
}

