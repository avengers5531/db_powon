<?php
namespace Powon\Entity;

class Invoice
{
    private $invoice_id;
    private $account_holder;
    private $amount_due;
    private $payment_deadline;
    private $date_paid;
    private $billing_period_start;
    private $billing_period_end;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data)
    {
        $this->invoice_id = $data['invoice_id'];
        $this->account_holder = $data['account_holder'];
        $this->amount_due = $data['amount_due'];
        $this->payment_deadline = $data['payment_deadline'];
        if(isset($data['date_paid'])){
            $this->date_paid = $data['date_paid'];
        }
        $this->billing_period_start = $data['billing_period_start'];
        $this->billing_period_end = $data['billing_period_end'];
    }

    /**
     * @return int
     */
    public function getInvoiceId(){
        return $this->invoice_id;
    }

    /**
     * @return int
     */
    public function getAccountHolder(){
        return $this->account_holder;
    }

    /**
     * @return double
     */
    public function getAmountDue(){
        return $this->amount_due;
    }

    /**
     * @return string
     */
    public function getPaymentDeadline(){
        return $this->payment_deadline;
    }

    /**
     * @return string
     */
    public function getDatePaid(){
        return $this->date_paid;
    }

    /**
     * @return string
     */
    public function getBillingStart(){
        return $this->billing_period_start;
    }

    /**
     * @return string
     */
    public function getBillingEnd(){
        return $this->billing_period_end;
    }

    /**
     * @param string date invoice paid
     */
    public function setDatePaid($date_paid){
        $this->date_paid = $date_paid;
    }

}