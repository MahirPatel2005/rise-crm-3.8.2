<?php

namespace Recruitment_management\Models;

class Recruitment_application_forms_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_application_forms';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $recruitment_application_forms_table = $this->db->prefixTable('recruitment_application_forms');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $recruitment_application_forms_table.id=$id";
        }

        $sql = "SELECT $recruitment_application_forms_table.*
        FROM $recruitment_application_forms_table
        WHERE $recruitment_application_forms_table.deleted=0 $where
        ORDER BY $recruitment_application_forms_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_recruitment_application_form_setting($setting_name) {
        $result = $this->db_builder->getWhere(array('key_name' => $setting_name), 1);
        if (count($result->getResult()) == 1) {
            return $result->getRow()->required;
        }
    }

}
