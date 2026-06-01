<?php

namespace SMS\Models;

class Sms_notification_settings_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sms_notification_settings';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $sms_notification_settings_table = $this->db->prefixTable('sms_notification_settings');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $sms_notification_settings_table.id=$id";
        }

        $category = get_array_value($options, "category");
        if ($category) {
            $where .= " AND $sms_notification_settings_table.category='$category'";
        }

        $sql = "SELECT $sms_notification_settings_table.*
        FROM $sms_notification_settings_table
        WHERE $sms_notification_settings_table.deleted=0 $where 
        ORDER BY $sms_notification_settings_table.sort ASC";

        return $this->db->query($sql);
    }

}
