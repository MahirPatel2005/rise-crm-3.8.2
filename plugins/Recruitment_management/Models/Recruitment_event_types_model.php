<?php

namespace Recruitment_management\Models;

class Recruitment_event_types_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_event_types';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $event_types_table = $this->db->prefixTable('recruitment_event_types');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $event_types_table.id=$id";
        }

        $sql = "SELECT $event_types_table.*
        FROM $event_types_table
        WHERE $event_types_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
