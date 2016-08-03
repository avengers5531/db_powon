<?php
namespace Powon\Services;

use Powon\Entity\Invoice;
use Powon\Entity\Member;


interface InvoiceService {
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
    
}