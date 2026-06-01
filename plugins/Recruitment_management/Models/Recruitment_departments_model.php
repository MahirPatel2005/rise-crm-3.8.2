<?php

namespace Recruitment_management\Models;

class Recruitment_departments_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_departments';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $department_table = $this->db->prefixTable('recruitment_departments');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $department_table.id=$id";
        }

        $sql = "SELECT $department_table.*
        FROM $department_table
        WHERE $department_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
