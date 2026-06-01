<?php

namespace Recruitment_management\Models;

class Recruitment_job_types_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_job_types';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $job_types_table = $this->db->prefixTable('recruitment_job_types');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $job_types_table.id=$id";
        }

        $sql = "SELECT $job_types_table.*
        FROM $job_types_table
        WHERE $job_types_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
