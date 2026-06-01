<?php

namespace Recruitment_management\Models;

class Recruitment_job_templates_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_job_templates';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $recruitment_job_templates_table = $this->db->prefixTable('recruitment_job_templates');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $recruitment_job_templates_table.id=$id";
        }

        $sql = "SELECT $recruitment_job_templates_table.*
        FROM $recruitment_job_templates_table
        WHERE $recruitment_job_templates_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
