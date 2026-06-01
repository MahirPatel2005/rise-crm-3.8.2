<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Timecamp Integration
  Description: Sync your projects and tasks from RISE to Timecamp. 
  Version: 1.0
  Requires at least: 2.9.2
  Author: codenplug
  Author URL: https://codecanyon.net/user/codenplug
 */

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["setup"][] = array("name" => "timecamp_integration", "url" => "timecamp_integration/settings");
    return $settings_menu;
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Timecamp_Integration', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("timecamp_integration/settings"), app_lang("settings"))
    );

    return $action_links_array;
});

//install dependencies
register_installation_hook("Timecamp_Integration", function ($item_purchase_code) {
    include PLUGINPATH . "Timecamp_Integration/install/do_install.php";
});

//uninstallation: remove data from database
register_uninstallation_hook("Timecamp_Integration", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "timecamp_integration_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "timecamp_integration`;";
    $db->query($sql_query);
});

//update plugin
use Timecamp_Integration\Controllers\Timecamp_Integration_Updates;

register_update_hook("Timecamp_Integration", function () {
    $update = new Timecamp_Integration_Updates();
    return $update->index();
});

//insert/update new task/projects to timecamp
if (!function_exists('insert_or_update_data_of_timecamp')) {

    function insert_or_update_data_of_timecamp($data) {
        if (!(get_timecamp_integration_setting("enable_timecamp") && get_timecamp_integration_setting("timecamp_api_token") && get_timecamp_integration_setting("timecamp_sync"))) {
            return true;
        }

        $table = get_array_value($data, "table");
        $dbprefix = get_db_prefix();
        $is_project = ($table === ($dbprefix . "projects") ? true : false);
        $is_task = ($table === ($dbprefix . "tasks") ? true : false);

        if (!($is_project || $is_task)) {
            //action for another table, exit from here
            return true;
        }

        $timecamp_sync = get_timecamp_integration_setting("timecamp_sync");
        $id = get_array_value($data, "id");
        $field_values = get_array_value($data, "data");
        $Timecamp_Integration_model = new \Timecamp_Integration\Models\Timecamp_Integration_model();
        $timecamp_task_title = $is_project ? get_array_value($field_values, "title") : ($id . " - " . get_array_value($field_values, "title"));

        //check if this project or the project of this task is syncable
        if ($timecamp_sync !== "all") {
            $project_id = $id;
            if ($is_task) {
                $project_id = get_array_value($field_values, "project_id");
            }

            $timecamp_sync_projects_array = explode(',', $timecamp_sync);
            if (!in_array($project_id, $timecamp_sync_projects_array)) {
                return true;
            }
        }

        if ($is_project) {
            $options = array("project_id" => $id);
        } else {
            $options = array("task_id" => $id);
        }

        $existing = $Timecamp_Integration_model->get_details($options)->getRow();
        if ($existing) { //update
            save_task_to_timecamp_with_curl($timecamp_task_title, 0, $existing->timecamp_task_id);
        } else { //insert
            if ($is_project) {
                //add this project
                $curl_response = save_task_to_timecamp_with_curl($timecamp_task_title);
                $timecamp_task_id_of_project = get_array_value($curl_response, "timecamp_task_id");
                save_timecamp_data(0, $id, $timecamp_task_id_of_project);
            } else {
                //for task, get the project for parent_id
                $project_id = get_array_value($field_values, "project_id");
                $options = array("project_id" => $project_id);
                $project_timecamp_data = $Timecamp_Integration_model->get_details($options)->getRow();
                if ($project_timecamp_data) {
                    $curl_response = save_task_to_timecamp_with_curl($timecamp_task_title, $project_timecamp_data->timecamp_task_id);
                    $timecamp_task_id = get_array_value($curl_response, "timecamp_task_id");
                    save_timecamp_data($id, $project_id, $timecamp_task_id);
                }
            }
        }
    }

}

register_data_insert_hook(function ($data) {
    insert_or_update_data_of_timecamp($data);
});

register_data_update_hook(function ($data) {
    insert_or_update_data_of_timecamp($data);
});

//delete task/project from timecamp
register_data_delete_hook(function ($data) {
    if (!(get_timecamp_integration_setting("enable_timecamp") && get_timecamp_integration_setting("timecamp_api_token") && get_timecamp_integration_setting("timecamp_sync"))) {
        return true;
    }

    $type = "";
    $table = get_array_value($data, "table");

    if ($table === (get_db_prefix() . "projects")) {
        $type = "project";
    } else if ($table === (get_db_prefix() . "tasks")) {
        $type = "task";
    }

    if (!$type) {
        return true;
    }

    $id = get_array_value($data, "id");
    $Timecamp_Integration_model = new \Timecamp_Integration\Models\Timecamp_Integration_model();

    if ($type === "project") {
        $options = array("project_id" => $id);
    } else {
        $options = array("task_id" => $id);
    }

    $existing = $Timecamp_Integration_model->get_details($options)->getRow();
    if ($existing) {
        delete_task_of_timecamp_with_curl($existing->timecamp_task_id);
        $Timecamp_Integration_model->delete($existing->id);

        //update timecamp project setting if specific projects is selected
        $timecamp_sync = get_timecamp_integration_setting("timecamp_sync");
        if ($type === "project" && $timecamp_sync !== "all") {
            $timecamp_sync = explode(',', $timecamp_sync);
            if (($key = array_search($id, $timecamp_sync)) !== false) {
                unset($timecamp_sync[$key]);

                $Timecamp_Integration_settings_model = new \Timecamp_Integration\Models\Timecamp_Integration_settings_model();
                $Timecamp_Integration_settings_model->save_setting("timecamp_sync", implode(',', $timecamp_sync));
            }
        }

        //delete all tasks from timecamp integration table when a project deleted
        if ($type === "project") {
            $Timecamp_Integration_model->delete_tasks_of_project($id);
        }
    }
});
