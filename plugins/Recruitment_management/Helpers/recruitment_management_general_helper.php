<?php

use App\Controllers\Security_Controller;

/**
 * link the css files
 * 
 * @param array $array
 * @return print css links
 */
if (!function_exists('recruitment_load_css')) {

    function recruitment_load_css(array $array) {
        $version = get_setting("app_version");

        foreach ($array as $uri) {
            echo "<link rel='stylesheet' type='text/css' href='" . base_url($uri) . "?v=$version' />";
        }
    }

}

if (!function_exists('get_available_job_variables')) {

    function get_available_job_variables() {
        return array(
            "CIRCULAR_ID",
            "START_DATE",
            "DEADLINE",
            "JOB_TITLE",
            "JOB_TYPE",
            "JOB_POSITION",
            "DEPARTMENTS",
            "JOB_LOCATION",
            "QUANTITY_TO_BE_REQUIRED",
            "SALARY",
            "DESCRIPTION",
            "APP_TITLE",
            "APPLY_JOB_LINK"
        );
    }

}

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_recruitment_setting')) {

    function get_recruitment_setting($key = "") {
        $recruitment_settings_model = new \Recruitment_management\Models\Recruitment_settings_model();
        return $recruitment_settings_model->get_recruitment_setting($key);
    }

}

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_recruitment_application_form_setting')) {

    function get_recruitment_application_form_setting($key = "") {
        $recruitment_application_model = new \Recruitment_management\Models\Recruitment_application_forms_model();
        return $recruitment_application_model->get_recruitment_application_form_setting($key);
    }

}


/**
 * get all data to make an recruitment circular
 * 
 * @param Int $job_id
 * @return array
 */
if (!function_exists('get_recruitment_circular_making_data')) {

    function get_recruitment_circular_making_data($job_id) {
        $Recruitment_jobs_model = new \Recruitment_management\Models\Recruitment_jobs_model();
        $circular_info = $Recruitment_jobs_model->get_details(array("id" => $job_id))->getRow();
        if ($circular_info) {
            $data['circular_info'] = $circular_info;
            return $data;
        }
    }

}


/**
 * get all data to make an recruitment
 * 
 * @param proposal making data $circular_data
 * @return array
 */
if (!function_exists('prepare_recruitment_circular_view')) {

    function prepare_recruitment_circular_view($circular_data) {
        if ($circular_data) {
            $circular_info = get_array_value($circular_data, "circular_info");

            $color = get_recruitment_setting("recruitment_job_circular_color");
            if (!$color) {
                $color = get_setting("invoice_color");
            }

            $data = array(
                "color" => $color,
                "circular_info" => $circular_info
            );

            $parser_data = array();

            $parser_data["CIRCULAR_ID"] = get_proposal_id($circular_info->id);
            $parser_data["START_DATE"] = format_to_date($circular_info->start_date, false);
            $parser_data["DEADLINE"] = format_to_date($circular_info->deadline, false);
            $parser_data["JOB_TITLE"] = $circular_info->job_title;
            $parser_data["JOB_TYPE"] = $circular_info->job_type_title;
            $parser_data["JOB_POSITION"] = $circular_info->job_position_title;
            $parser_data["DEPARTMENTS"] = $circular_info->department_title;
            $parser_data["JOB_LOCATION"] = $circular_info->address;
            $parser_data["QUANTITY_TO_BE_REQUIRED"] = $circular_info->quantity_to_be_required;
            $parser_data["SALARY"] = $circular_info->salary;
            $parser_data["DESCRIPTION"] = $circular_info->description;
            $parser_data["APP_TITLE"] = get_setting("app_title");
            $parser_data["APPLY_JOB_LINK"] = get_uri("recruitment_circulars/apply_circular_modal_form/$circular_info->id");

            $parser = \Config\Services::parser();
            $content = remove_custom_field_titles_from_variables($circular_info->content);
            $recruitment_circular_view = $parser->setData($parser_data)->renderString($content);
            $recruitment_circular_view = htmlspecialchars_decode($recruitment_circular_view);

            return $recruitment_circular_view;
        }
    }

}

if (!function_exists('recruitment_count_active_circulars')) {

    function recruitment_count_active_circulars() {
        $instance = new Security_Controller();

        $options = array("status" => "active");

        if (!$instance->login_user->is_admin) {
            $options["user_id"] = $instance->login_user->id;
        }

        $Recruitment_jobs_model = new Recruitment_management\Models\Recruitment_jobs_model();
        return count($Recruitment_jobs_model->get_details($options)->getResult());
    }

}
