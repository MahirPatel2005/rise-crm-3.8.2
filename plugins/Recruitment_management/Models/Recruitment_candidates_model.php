<?php

namespace Recruitment_management\Models;

class Recruitment_candidates_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_candidates';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $recruitment_candidates_table = $this->db->prefixTable('recruitment_candidates');
        $jobs_table = $this->db->prefixTable('recruitment_jobs');
        $Recruitment_hiring_stages_table = $this->db->prefixTable('recruitment_hiring_stages');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $recruitment_candidates_table.id=$id";
        }

        $hiring_stage = get_array_value($options, "hiring_stage");
        if ($hiring_stage) {
            $where .= " AND $recruitment_candidates_table.status_id=$hiring_stage";
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND FIND_IN_SET('$user_id', $jobs_table.recruiters)";
        }

        $circular_id = get_array_value($options, "circular_id");
        if ($circular_id) {
            $where .= " AND $recruitment_candidates_table.circular_id=$circular_id";
        }

        $sql = "SELECT $recruitment_candidates_table.*, $jobs_table.job_title AS circular_title, $Recruitment_hiring_stages_table.id AS hiring_stage_id, $Recruitment_hiring_stages_table.title AS hiring_stage, CONCAT($recruitment_candidates_table.first_name, ' ',$recruitment_candidates_table.last_name) AS name
        FROM $recruitment_candidates_table
        LEFT JOIN $jobs_table ON $jobs_table.id=$recruitment_candidates_table.circular_id
        LEFT JOIN $Recruitment_hiring_stages_table ON $Recruitment_hiring_stages_table.id=$recruitment_candidates_table.status_id
        WHERE $recruitment_candidates_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
