<?php

namespace Powon\Services\Implementation;

use Powon\Dao\InvoiceDAO;
use Powon\Entity\Invoice;

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
     * @return Invoice[] of invoice entities.
     */

    public function getInvoiceByMember($member_id)
    {
        try {
            return $this->getInvoiceByMember($member_id);
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
            return $this->memberDAO->getAllMembers();
        } catch (\PDOException $ex) {
            $this->log->error("A pdo exception occurred: " . $ex->getMessage());
            return [];
        }
    }
}

