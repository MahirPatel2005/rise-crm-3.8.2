<?php

namespace SMS\Models;

class Sms_templates_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sms_templates';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $Sms_templates_table = $this->db->prefixTable('sms_templates');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $Sms_templates_table.id=$id";
        }

        $sql = "SELECT $Sms_templates_table.*
        FROM $Sms_templates_table
        WHERE $Sms_templates_table.deleted=0 $where";

        return $this->db->query($sql);
    }

    function get_final_sms_template($template_name = "") {
        $email_templates_table = $this->db->prefixTable('sms_templates');

        $sql = "SELECT $email_templates_table.default_message, $email_templates_table.custom_message, 
            signature_template.custom_message AS signature_custom_message, signature_template.default_message AS signature_default_message
        FROM $email_templates_table
        LEFT JOIN $email_templates_table AS signature_template ON signature_template.template_name='signature'
        WHERE $email_templates_table.deleted=0 AND $email_templates_table.template_name='$template_name'";
        $result = $this->db->query($sql)->getRow();

        $info = new \stdClass();
        $info->message = $result->custom_message ? $result->custom_message : $result->default_message;
        $info->signature = $result->signature_custom_message ? $result->signature_custom_message : $result->signature_default_message;

        return $info;
    }

}
