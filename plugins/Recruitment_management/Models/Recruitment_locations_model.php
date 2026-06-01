<?php

namespace Recruitment_management\Models;

class Recruitment_locations_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'recruitment_locations';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $locations_table = $this->db->prefixTable('recruitment_locations');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $locations_table.id=$id";
        }

        $sql = "SELECT $locations_table.*
        FROM $locations_table
        WHERE $locations_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
