<?php

namespace SMS\Models;

class Sms_notification_logs_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sms_notification_logs';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $Sms_notification_logs_table = $this->db->prefixTable('sms_notification_logs');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $Sms_notification_logs_table.id=$id";
        }

        $sql = "SELECT $Sms_notification_logs_table.*
        FROM $Sms_notification_logs_table
        WHERE $Sms_notification_logs_table.deleted=0 $where";

        return $this->db->query($sql);
    }

}
