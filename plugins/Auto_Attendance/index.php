<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Auto Attendance
  Description: Allow your team members to clock in/out automatically.
  Version: 1.0
  Requires at least: 3.1
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler
 */

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["plugins"][] = array("name" => "auto_attendance", "url" => "auto_attendance_settings");
    return $settings_menu;
});

//install dependencies
register_installation_hook("Auto_Attendance", function ($item_purchase_code) {
    include PLUGINPATH . "Auto_Attendance/install/do_install.php";
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Auto_Attendance', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("auto_attendance_settings"), app_lang("settings"))
    );

    return $action_links_array;
});

//update plugin
use Auto_Attendance\Controllers\Auto_Attendance_Updates;

register_update_hook("Auto_Attendance", function () {
    $update = new Auto_Attendance_Updates();
    return $update->index();
});

//uninstallation: remove data from database
register_uninstallation_hook("Auto_Attendance", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "auto_attendance_settings`;";
    $db->query($sql_query);
});

use App\Controllers\Security_Controller;

//clock in after sign in
app_hooks()->add_action('app_hook_after_signin', function () {
    $auto_clock_in_on_signin = get_auto_attendance_setting("auto_clock_in_on_signin");

    if ($auto_clock_in_on_signin) {
        $instance = new Security_Controller();
        auto_attendance_log_time($instance->login_user, "clock_in");
    }
});

//clock out before sign out
app_hooks()->add_action('app_hook_before_signout', function () {
    $auto_clock_out_on_signout = get_auto_attendance_setting("auto_clock_out_on_signout");

    if ($auto_clock_out_on_signout) {
        $instance = new Security_Controller();
        auto_attendance_log_time($instance->login_user, "clock_out");
    }
});

if (!function_exists("auto_attendance_log_time")) {

    function auto_attendance_log_time($user_info, $type = "") {

        //this module is accessible only to team members 
        if ($user_info->user_type !== "staff") {
            return false;
        }

        //check ip restriction for non-admin users
        if (!$user_info->is_admin) {
            $ip = get_real_ip();
            $Settings_model = model("App\Models\Settings_model");
            $allowed_ips = $Settings_model->get_setting("allowed_ip_addresses");
            if ($allowed_ips) {
                $allowed_ip_array = array_map('trim', preg_split('/\R/', $allowed_ips));
                if (!in_array($ip, $allowed_ip_array)) {
                    return false;
                }
            }
        }

        $Attendance_model = model("App\Models\Attendance_model");
        $current_clock_record = $Attendance_model->current_clock_in_record($user_info->id);
        if (($type === "clock_in" && $current_clock_record && $current_clock_record->id) || ($type === "clock_out" && !$current_clock_record)) {
            //don't toggle operation, operate only the instructed operation
            return true;
        }

        $Attendance_model->log_time($user_info->id);
    }

}

//auto clock out after certain time
app_hooks()->add_action('app_hook_after_cron_run', function () {
    try {
        $auto_clock_out_after = get_auto_attendance_setting("auto_clock_out_after");
        if ($auto_clock_out_after) {

            //get clocked in team members
            $Attendance_model = model("App\Models\Attendance_model");

            //prepare last activity date accroding to the setting
            $now = get_current_utc_time();
            $start_date = subtract_period_from_date($now, $auto_clock_out_after, "hours", "Y-m-d H:i:s");
            $clocked_in_users = $Attendance_model->get_details(array(
                        "end_date" => $start_date,
                        "only_clocked_in_members" => true,
                        "access_type" => "all" //this should be applied on all users
                    ))->getResult();

            foreach ($clocked_in_users as $user) {
                $Users_model = model("App\Models\Users_model");
                $user_info = $Users_model->get_one($user->user_id);
                auto_attendance_log_time($user_info);
            }
        }
    } catch (\Exception $ex) {
        log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
    }
});
