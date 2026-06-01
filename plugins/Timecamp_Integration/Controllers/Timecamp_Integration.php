<?php

namespace Timecamp_Integration\Controllers;

use App\Controllers\Security_Controller;

class Timecamp_Integration extends Security_Controller {

    protected $Timecamp_Integration_settings_model;
    protected $Timecamp_Integration_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Timecamp_Integration_settings_model = new \Timecamp_Integration\Models\Timecamp_Integration_settings_model();
        $this->Timecamp_Integration_model = new \Timecamp_Integration\Models\Timecamp_Integration_model();
    }

    function index() {
        show_404();
    }

    function settings() {
        $project_options = array("status" => "open");
        $projects = $this->Projects_model->get_details($project_options)->getResult();
        $projects_dropdown = array();

        if ($projects) {
            foreach ($projects as $project) {
                $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
            }
        }

        $view_data["projects_dropdown"] = $projects_dropdown;

        //generate sync statuses
        $timecamp_sync = get_timecamp_integration_setting("timecamp_sync");
        $options = array("timecamp_sync" => $timecamp_sync);
        $result = $this->Timecamp_Integration_model->count_synced_items($options);

        $view_data["projects_count"] = $result->projects_count;
        $view_data["syncable_projects_count"] = strval(($timecamp_sync === "all") ? count($projects) : count(explode(',', $timecamp_sync)));
        $view_data["tasks_count"] = $result->tasks_count;
        $view_data["syncable_tasks_count"] = $result->syncable_tasks_count;

        return $this->template->rander("Timecamp_Integration\Views\settings\index", $view_data);
    }

    function save_settings() {
        $settings = array(
            "enable_timecamp", "timecamp_api_token", "timecamp_sync"
        );

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);

            if ($setting === "timecamp_sync" && $value === "specific") {
                $value = $this->request->getPost("timecamp_sync_specific");
            }

            if (is_null($value)) {
                $value = "";
            }

            $this->Timecamp_Integration_settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function sync() {
        if (!(get_timecamp_integration_setting("enable_timecamp") && get_timecamp_integration_setting("timecamp_api_token") && get_timecamp_integration_setting("timecamp_sync"))) {
            show_404();
        }

        //execute maximum 300 seconds 
        ini_set('max_execution_time', 300);

        //get projects
        $options = array("timecamp_sync" => get_timecamp_integration_setting("timecamp_sync"));
        $projects = $this->Timecamp_Integration_model->get_syncable_projects($options)->getResult();
        foreach ($projects as $project) {
            $timecamp_task_id_of_project = $this->is_this_task_or_project_synced(0, $project->id);
            if (!$timecamp_task_id_of_project) {
                $timecamp_task_id_of_project = $this->save_task_to_timecamp($project->title);
                save_timecamp_data(0, $project->id, $timecamp_task_id_of_project);
            }

            //save tasks of this project
            $tasks = $this->Tasks_model->get_all_where(array("project_id" => $project->id, "deleted" => 0))->getResult();
            foreach ($tasks as $task) {
                if (!$this->is_this_task_or_project_synced($task->id)) {
                    $task_name = $task->id . " - " . $task->title;
                    $timecamp_task_id = $this->save_task_to_timecamp($task_name, $timecamp_task_id_of_project);
                    save_timecamp_data($task->id, $task->project_id, $timecamp_task_id);
                }
            }
        }

        echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
    }

    private function is_this_task_or_project_synced($task_id = 0, $project_id = 0) {
        $options = array(
            "task_id" => $task_id,
            "project_id" => $project_id
        );

        $existing = $this->Timecamp_Integration_model->get_details($options)->getRow();
        if ($existing) {
            return $existing->timecamp_task_id;
        }
    }

    private function save_task_to_timecamp($name = "", $parent_id = 0) {
        $curl_response = save_task_to_timecamp_with_curl($name, $parent_id);
        $response = get_array_value($curl_response, "response");
        $err = get_array_value($curl_response, "err");

        if ($err) {
            echo json_encode(array("success" => false, 'message' => "cURL Error #:" . $err));
            exit;
        } else {
            try {
                $response = json_decode($response);
                if (!isset(reset($response)->task_id)) {
                    //this is not the exact array
                    echo json_encode(array("success" => false, 'message' => isset($response->message) ? $response->message : app_lang('error_occurred')));
                    exit;
                }

                return reset($response)->task_id;
            } catch (\Exception $ex) {
                echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
                exit;
            }
        }
    }

}
