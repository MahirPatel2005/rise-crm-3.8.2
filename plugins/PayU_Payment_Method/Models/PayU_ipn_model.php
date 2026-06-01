<?php

namespace PayU_Payment_Method\Models;

use App\Models\Crud_model;

class PayU_ipn_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'payu_ipn';
        parent::__construct($this->table);
    }

    function get_one_payment_where($payment_verification_code) {
        $payu_ipn_table = $this->db->prefixTable('payu_ipn');

        $sql = "SELECT $payu_ipn_table.*
        FROM $payu_ipn_table
        WHERE $payu_ipn_table.deleted=0 AND $payu_ipn_table.payment_verification_code='$payment_verification_code'
        ORDER BY $payu_ipn_table.id DESC
        LIMIT 1";

        return $this->db->query($sql)->getRow();
    }

}
