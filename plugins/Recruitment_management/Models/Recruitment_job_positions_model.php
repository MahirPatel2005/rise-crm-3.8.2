<?php

namespace Recruitment_management\Models;

class Recruitment_job_positions_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_job_positions';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $job_positions_table = $this->db->prefixTable('recruitment_job_positions');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $job_positions_table.id=$id";
        }

        $sql = "SELECT $job_positions_table.*
        FROM $job_positions_table
        WHERE $job_positions_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
