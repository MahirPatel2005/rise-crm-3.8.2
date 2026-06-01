<?php

namespace Recruitment_management\Models;

class Recruitment_hiring_stages_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_hiring_stages';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $hiring_stages_table = $this->db->prefixTable('recruitment_hiring_stages');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $hiring_stages_table.id=$id";
        }

        $sql = "SELECT $hiring_stages_table.*
        FROM $hiring_stages_table
        WHERE $hiring_stages_table.deleted=0 $where
        ORDER BY $hiring_stages_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_candidate_statistics($circular_id) {
        $candidates_table = $this->db->prefixTable('recruitment_candidates');
        $hiring_stages_table = $this->db->prefixTable('recruitment_hiring_stages');

        $where = "";

        if ($circular_id) {
            $where .= " AND $candidates_table.circular_id=$circular_id";
        }

        $sql = "SELECT COUNT($candidates_table.id) AS total, $hiring_stages_table.id AS hiring_stage_id
                From $hiring_stages_table
                LEFT JOIN $candidates_table ON $candidates_table.status_id=$hiring_stages_table.id AND $candidates_table.deleted=0
                WHERE $hiring_stages_table.deleted=0 $where
                GROUP BY $hiring_stages_table.id
                ORDER BY $hiring_stages_table.sort ASC";

        return $this->db->query($sql)->getResult();
    }

}
