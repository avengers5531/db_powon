<?php
namespace Powon\Dao;

use Powon\Entity\Invoice;

interface InvoiceDAO
{

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
     * @param $invoice_id
     * @param $member
     * @return
     */
    public function payInvoice($invoice_id, $member);

        }