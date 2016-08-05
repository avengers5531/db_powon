<?php

namespace Powon\Dao\Implementation;

use Powon\Dao\InvoiceDAO as InvoiceDAO;
use Powon\Entity\Invoice as Invoice;
use Powon\Utils\DateTimeHelper as DTHelp;
use Powon\Utils\DateTimeHelper;

class InvoiceDAOImpl implements InvoiceDAO
{
    private $db;

    /**
     * InvoiceDaoImpl constructor.
     * @param \PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return Invoice|null
     */
    public function getInvoiceById($invoice_id)
    {
        $sql = 'SELECT invoice.invoice_id,
        invoice.account_holder,
        invoice.amount_due,
        invoice.payment_deadline,
        invoice.date_paid,
        invoice.billing_period_start,
        invoice.billing_period_end
        FROM invoice 
        WHERE invoice.invoice_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $invoice_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return ($row ? new Invoice($row) : null);
        } else {
            return null;
        }
    }

    /**
     * @return Invoice[] of invoice entities.
     */
    public function getInvoiceByMember($member_id)
    {
        $sql = 'SELECT invoice.invoice_id,
        invoice.account_holder,
        invoice.amount_due,
        invoice.payment_deadline,
        invoice.date_paid,
        invoice.billing_period_start,
        invoice.billing_period_end
        FROM invoice
        WHERE invoice.account_holder = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $member_id, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
            return array_map(function ($row) {
                return new Invoice($row);
            }, $results);
        } else {
            return [];
        }
    }

    /**
     * @return Invoice[] of invoice entities.
     */

    public function getUnpaidInvoices()
    {

        $sql = 'SELECT invoice.invoice_id,
        invoice.account_holder,
        invoice.amount_due,
        invoice.payment_deadline,
        invoice.date_paid,
        invoice.billing_period_start,
        invoice.billing_period_end
        FROM invoice
        WHERE invoice.date_paid IS NULL';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        if ($results) {
            return array_map(function ($row) {
                return new Invoice($row);
            }, $results);
        } else {
            return [];
        }
    }

    /**
     * @param $invoice_id
     * @param $member
     * @return bool
     */
    public function payInvoice($invoice_id, $member){
        $sql = 'UPDATE invoice 
                SET date_paid = :currentdate
                WHERE invoice_id = :invoice_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':currentdate', DateTimeHelper::getCurrentTimeStamp(), \PDO::PARAM_STR);
        $stmt->bindValue(':invoice_id', $invoice_id, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

