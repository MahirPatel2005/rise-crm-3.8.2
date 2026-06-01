<?php

namespace Recruitment_management\Models;

class Recruitment_jobs_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_jobs';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $jobs_table = $this->db->prefixTable('recruitment_jobs');
        $recruitment_job_types_table = $this->db->prefixTable('recruitment_job_types');
        $recruitment_job_positions_table = $this->db->prefixTable('recruitment_job_positions');
        $recruitment_departments_table = $this->db->prefixTable('recruitment_departments');
        $recruitment_locations_table = $this->db->prefixTable('recruitment_locations');
        $users_table = $this->db->prefixTable('users');

        $where = "";

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $jobs_table.id=$id";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $jobs_table.status='$status'";
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND FIND_IN_SET('$user_id', $jobs_table.recruiters)";
        }

        $sql = "SELECT $jobs_table.*, $recruitment_job_types_table.title AS job_type_title, $recruitment_job_positions_table.title AS job_position_title, $recruitment_departments_table.title AS department_title, $recruitment_locations_table.address AS address,
                (SELECT GROUP_CONCAT($users_table.id, '--::--', $users_table.first_name, ' ', $users_table.last_name, '--::--' , IFNULL($users_table.image,'')) FROM $users_table WHERE $users_table.deleted=0 AND FIND_IN_SET($users_table.id, $jobs_table.recruiters)) AS recruiters_list
        FROM $jobs_table
        LEFT JOIN $recruitment_job_types_table ON $jobs_table.job_type_id = $recruitment_job_types_table.id
        LEFT JOIN $recruitment_job_positions_table ON $jobs_table.job_position_id = $recruitment_job_positions_table.id
        LEFT JOIN $recruitment_departments_table ON $jobs_table.department_id = $recruitment_departments_table.id
        LEFT JOIN $recruitment_locations_table ON $jobs_table.location_id = $recruitment_locations_table.id
        LEFT JOIN $users_table ON $users_table.id=$jobs_table.recruiters
        WHERE $jobs_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
