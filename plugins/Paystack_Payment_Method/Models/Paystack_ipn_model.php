<?php

namespace Paystack_Payment_Method\Models;

use App\Models\Crud_model;

class Paystack_ipn_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'paystack_ipn';
        parent::__construct($this->table);
    }

    function get_one_payment_where($payment_verification_code) {
        $paystack_ipn_table = $this->db->prefixTable('paystack_ipn');

        $sql = "SELECT $paystack_ipn_table.*
        FROM $paystack_ipn_table
        WHERE $paystack_ipn_table.deleted=0 AND $paystack_ipn_table.payment_verification_code='$payment_verification_code'
        ORDER BY $paystack_ipn_table.id DESC
        LIMIT 1";

        return $this->db->query($sql)->getRow();
    }

}
