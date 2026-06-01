<?php

namespace Timecamp_Integration\Models;

use App\Models\Crud_model;

class Timecamp_Integration_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'timecamp_integration';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $timecamp_integration = $this->db->prefixTable('timecamp_integration');

        $where = "";

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $timecamp_integration.id=$id";
        }

        $task_id = get_array_value($options, "task_id");
        if ($task_id) {
            $where .= " AND $timecamp_integration.task_id=$task_id";
        }

        $project_id = get_array_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $timecamp_integration.project_id=$project_id AND $timecamp_integration.task_id=0";
        }

        $sql = "SELECT $timecamp_integration.*
        FROM $timecamp_integration
        WHERE $timecamp_integration.deleted=0 $where";

        return $this->db->query($sql);
    }

    function count_synced_items($options = array()) {
        $timecamp_integration = $this->db->prefixTable('timecamp_integration');
        $tasks_table = $this->db->prefixTable('tasks');

        $timecamp_integration_where = "";
        $tasks_where = "";

        $timecamp_sync = get_array_value($options, "timecamp_sync");
        if ($timecamp_sync && $timecamp_sync !== "all") {
            $timecamp_integration_where = " AND $timecamp_integration.project_id IN($timecamp_sync)";
            $tasks_where = " AND $tasks_table.project_id IN($timecamp_sync)";
        }

        $projects_count_sql = "SELECT COUNT($timecamp_integration.id) AS projects_count
        FROM $timecamp_integration
        WHERE $timecamp_integration.deleted=0 AND $timecamp_integration.task_id=0 $timecamp_integration_where";

        $tasks_count_sql = "SELECT COUNT($timecamp_integration.id) AS tasks_count
        FROM $timecamp_integration
        WHERE $timecamp_integration.deleted=0 AND $timecamp_integration.task_id!=0 $timecamp_integration_where";

        $syncable_tasks_count_sql = "SELECT COUNT($tasks_table.id) AS syncable_tasks_count
        FROM $tasks_table
        WHERE $tasks_table.deleted=0 $tasks_where";

        $result = new \stdClass();
        $result->projects_count = $this->db->query($projects_count_sql)->getRow()->projects_count;
        $result->tasks_count = $this->db->query($tasks_count_sql)->getRow()->tasks_count;
        $result->syncable_tasks_count = $this->db->query($syncable_tasks_count_sql)->getRow()->syncable_tasks_count;

        return $result;
    }

    function get_syncable_projects($options = array()) {
        $projects_table = $this->db->prefixTable('projects');

        $where = "";
        $timecamp_sync = get_array_value($options, "timecamp_sync");
        if ($timecamp_sync !== "all") {
            $where = " AND $projects_table.id IN($timecamp_sync)";
        }

        $sql = "SELECT $projects_table.id, $projects_table.title
        FROM $projects_table
        WHERE $projects_table.deleted=0 $where";

        return $this->db->query($sql);
    }

    function delete_tasks_of_project($project_id) {
        $timecamp_integration = $this->db->prefixTable('timecamp_integration');

        //delete tasks
        $delete_tasks_sql = "UPDATE $timecamp_integration SET $timecamp_integration.deleted=1 WHERE $timecamp_integration.project_id=$project_id; ";
        $this->db->query($delete_tasks_sql);
    }

}
