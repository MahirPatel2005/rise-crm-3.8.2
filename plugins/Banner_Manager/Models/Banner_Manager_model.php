<?php

namespace Banner_Manager\Models;

use App\Models\Crud_model;

class Banner_Manager_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'banner_manager';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $banner_manager_table = $this->db->prefixTable('banner_manager');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $banner_manager_table.id=$id";
        }

        $user_id = get_array_value($options, "user_id");
        $is_alert = get_array_value($options, "is_alert");
        if ($is_alert) {
            $now = get_my_local_time("Y-m-d");
            $where .= " AND start_date<='$now' AND end_date>='$now' AND FIND_IN_SET($user_id,$banner_manager_table.read_by) = 0 ";
        }

        $is_admin = get_array_value($options, "is_admin");
        if (!$is_admin && $user_id) {

            //find banners where share with the user or his/her team
            $team_ids = get_array_value($options, "team_ids");
            $team_search_sql = "";

            //searh for teams
            if ($team_ids) {
                $teams_array = explode(",", $team_ids);
                foreach ($teams_array as $team_id) {
                    $team_search_sql .= " OR (FIND_IN_SET('team:$team_id', $banner_manager_table.share_with_team_members)) ";
                }
            }


            $is_client = get_array_value($options, "is_client");
            if ($is_client) {
                //client user's can't see the banners which has shared with all team members
                $where .= " AND (FIND_IN_SET('all', $banner_manager_table.share_with_client_contacts) OR FIND_IN_SET('contact:$user_id', $banner_manager_table.share_with_client_contacts))";
            } else {
                //searh for user and teams
                $include_creator_sql = "$banner_manager_table.created_by=$user_id OR ";
                if ($is_alert) {
                    $include_creator_sql = "";
                }

                $where .= " AND ($include_creator_sql $banner_manager_table.share_with_team_members='all'
                    OR (FIND_IN_SET('member:$user_id', $banner_manager_table.share_with_team_members))
                        $team_search_sql
                        )";
            }

            $where .= " AND $users_table.deleted=0 AND $users_table.status='active'";
        }

        $sql = "SELECT $banner_manager_table.*, 
        CONCAT($users_table.first_name, ' ',$users_table.last_name) AS created_by_name, $users_table.image AS created_by_avatar
        FROM $banner_manager_table
        LEFT JOIN $users_table ON $users_table.id = $banner_manager_table.created_by
        WHERE $banner_manager_table.deleted=0 $where
        ORDER BY $banner_manager_table.start_date DESC";

        return $this->db->query($sql);
    }

    function get_client_contacts_list() {
        $users_table = $this->db->prefixTable('users');
        $clients_table = $this->db->prefixTable('clients');

        $sql = "SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $clients_table.company_name
        FROM $users_table
        LEFT JOIN $clients_table ON $clients_table.id = $users_table.client_id
        WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='client'
        ORDER BY $users_table.first_name ASC";
        return $this->db->query($sql);
    }

    function mark_as_read($id, $user_id) {
        $id = $id ? $this->db->escapeString($id) : $id;
        $banners_table = $this->db->prefixTable('banner_manager');
        $sql = "UPDATE $banners_table SET $banners_table.read_by = CONCAT($banners_table.read_by,',',$user_id)
        WHERE $banners_table.id=$id AND FIND_IN_SET($user_id,$banners_table.read_by) = 0";
        return $this->db->query($sql);
    }

}
