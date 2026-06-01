<?php

namespace Recruitment_management\Models;

class Recruitment_candidate_notes_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_candidate_notes';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $recruitment_candidate_notes_table = $this->db->prefixTable('recruitment_candidate_notes');
        $recruitment_candidates_table = $this->db->prefixTable('recruitment_candidates');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $recruitment_candidate_notes_table.id=$id";
        }

        $candidate_id = get_array_value($options, "candidate_id");
        if ($candidate_id) {
            $where .= " AND $recruitment_candidate_notes_table.candidate_id=$candidate_id";
        }

        $sql = "SELECT $recruitment_candidate_notes_table.*
        FROM $recruitment_candidate_notes_table
        LEFT JOIN $recruitment_candidates_table ON $recruitment_candidates_table.id=$recruitment_candidate_notes_table.candidate_id
        WHERE $recruitment_candidate_notes_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
