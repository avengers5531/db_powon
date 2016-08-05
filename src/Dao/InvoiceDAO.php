<?php
namespace Powon\Dao;

use Powon\Entity\Invoice;

interface InvoiceDAO {

    /**
     * @return Invoice
     */
    public function getInvoiceById($invoice_id);

    /**
     * @return Invoice[] of invoice entities.
     */

    public function getInvoiceByMember($member_id);

    /**
     * @return Invoice[] of invoice entities.
     */

    public function getUnpaidInvoices();

    /**
     * @param $member_id string|int
     * @param $start_date string date in YYYY-MM-DD hh:mm:ss format
     * @param $end_date string same as above
     * @return Invoice|null
     */
    public function getInvoiceForMemberWithBillingStartDateBetween($member_id, $start_date, $end_date);

    /**
     * @param $invoice Invoice
     * @return int The created invoice id (or -1 in case of error)
     */
    public function createInvoice($invoice);
    
}
