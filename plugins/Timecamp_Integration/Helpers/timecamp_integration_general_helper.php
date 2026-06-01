<?php

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_timecamp_integration_setting')) {

    function get_timecamp_integration_setting($key = "") {
        $config = new Timecamp_Integration\Config\Timecamp_Integration();

        $setting_value = get_array_value($config->app_settings_array, $key);
        if ($setting_value !== NULL) {
            return $setting_value;
        } else {
            return "";
        }
    }

}

if (!function_exists('save_task_to_timecamp_with_curl')) {

    function save_task_to_timecamp_with_curl($name = "", $parent_id = 0, $task_id = 0) {
        $req = "name=$name";

        if ($parent_id) {
            $req .= "&parent_id=$parent_id";
        }

        if ($task_id) { //update
            $req .= "&task_id=$task_id";
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://app.timecamp.com/third_party/api/tasks?format=json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $task_id ? "PUT" : "POST",
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_HTTPHEADER => [
                "authorization: " . get_timecamp_integration_setting("timecamp_api_token")
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        try {
            $response_array = json_decode($response);
            $timecamp_task_id = $task_id ? $response_array->task_id : reset($response_array)->task_id; //it's giving the array differently for insert/update
        } catch (\Exception $ex) {
            $timecamp_task_id = "";
        }

        return array(
            "response" => $response,
            "err" => $err,
            "timecamp_task_id" => $timecamp_task_id
        );
    }

}
if (!function_exists('delete_task_of_timecamp_with_curl')) {

    function delete_task_of_timecamp_with_curl($task_id = 0) {
        if (!$task_id) {
            return false;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://app.timecamp.com/third_party/api/tasks?task_id=$task_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "authorization: " . get_timecamp_integration_setting("timecamp_api_token")
            ],
        ]);

        curl_exec($curl);
        curl_close($curl);
    }

}

if (!function_exists('save_timecamp_data')) {

    function save_timecamp_data($task_id = 0, $project_id = 0, $timecamp_task_id = 0) {
        $data = array(
            "task_id" => $task_id,
            "project_id" => $project_id,
            "timecamp_task_id" => $timecamp_task_id
        );

        $Timecamp_Integration_model = new \Timecamp_Integration\Models\Timecamp_Integration_model();
        $Timecamp_Integration_model->ci_save($data);
    }

}