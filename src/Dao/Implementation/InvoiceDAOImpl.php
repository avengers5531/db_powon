<?php

namespace Powon\Dao\Implementation;

use Powon\Dao\InvoiceDAO as InvoiceDAO;
use Powon\Entity\Invoice as Invoice;

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
     * @param $member_id string|int
     * @param $start_date string date in YYYY-MM-DD hh:mm:ss format
     * @param $end_date string same as above
     * @return Invoice|null
     */
    public function getInvoiceForMemberWithBillingStartDateBetween($member_id, $start_date, $end_date)
    {
        $sql = "SELECT i.* FROM invoice i WHERE
                i.account_holder = :member_id
                AND i.billing_period_start BETWEEN :start_date AND :end_date";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':member_id', $member_id);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            if ($data) {
                return new Invoice($data);
            }
        }
        return null;
    }

    /**
     * @param $invoice Invoice
     * @return int The created invoice id (or -1 in case of error)
     */
    public function createInvoice($invoice)
    {
        $sql = "INSERT INTO invoice (amount_due, 
                payment_deadline, date_paid, billing_period_start,
                billing_period_end, account_holder) VALUES
                (:amount_due, :payment_deadline, :date_paid, :billing_period_start,
                :billing_period_end, :account_holder)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':amount_due', $invoice->getAmountDue());
        $stmt->bindValue(':payment_deadline', $invoice->getPaymentDeadline());
        $stmt->bindValue(':date_paid', $invoice->getDatePaid());
        $stmt->bindValue(':billing_period_start', $invoice->getBillingStart());
        $stmt->bindValue(':billing_period_end', $invoice->getBillingEnd());
        $stmt->bindValue(':account_holder', $invoice->getAccountHolder());
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return -1;
    }
}

