<?php

namespace Recruitment_management\Models;

class Recruitment_candidate_events_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_candidate_events';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $recruitment_candidate_events_table = $this->db->prefixTable('recruitment_candidate_events');
        $recruitment_event_types_table = $this->db->prefixTable('recruitment_event_types');
        $recruitment_candidates_table = $this->db->prefixTable('recruitment_candidates');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $recruitment_candidate_events_table.id=$id";
        }

        $candidate_id = get_array_value($options, "candidate_id");
        if ($candidate_id) {
            $where .= " AND $recruitment_candidate_events_table.candidate_id=$candidate_id";
        }

        $sql = "SELECT $recruitment_candidate_events_table.*, $recruitment_event_types_table.title AS event_title, CONCAT($recruitment_candidates_table.first_name, ' ',$recruitment_candidates_table.last_name) AS candidate_name
        FROM $recruitment_candidate_events_table
        LEFT JOIN $recruitment_event_types_table ON $recruitment_event_types_table.id=$recruitment_candidate_events_table.event_type_id
        LEFT JOIN $recruitment_candidates_table ON $recruitment_candidates_table.id= $recruitment_candidate_events_table.candidate_id
        WHERE $recruitment_candidate_events_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
