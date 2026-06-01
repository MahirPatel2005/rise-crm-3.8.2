<?php

use App\Controllers\Security_Controller;

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_skype_integration_setting')) {

    function get_skype_integration_setting($key = "") {
        $config = new Skype_Integration\Config\Skype_Integration();

        $setting_value = get_array_value($config->app_settings_array, $key);
        if ($setting_value !== NULL) {
            return $setting_value;
        } else {
            return "";
        }
    }

}

if (!function_exists('skype_integration_count_upcoming_meetings')) {

    function skype_integration_count_upcoming_meetings() {
        $instance = new Security_Controller();
        $is_client = false;
        if ($instance->login_user->user_type == "client") {
            $is_client = true;
        }

        $options = array(
            "is_admin" => $instance->login_user->is_admin,
            "user_id" => $instance->login_user->id,
            "team_ids" => $instance->login_user->team_ids,
            "is_client" => $is_client,
            "upcoming_only" => true
        );

        $Skype_meetings_model = new Skype_Integration\Models\Skype_meetings_model();
        return count($Skype_meetings_model->get_details($options)->getResult());
    }

}

if (!function_exists('can_manage_skype_integration')) {

    function can_manage_skype_integration() {
        $skype_integration_users = get_skype_integration_setting("skype_integration_users");
        $skype_integration_users = explode(',', $skype_integration_users);
        $instance = new Security_Controller();

        if ($instance->login_user->is_admin || in_array($instance->login_user->id, $skype_integration_users)) {
            return true;
        }
    }

}
