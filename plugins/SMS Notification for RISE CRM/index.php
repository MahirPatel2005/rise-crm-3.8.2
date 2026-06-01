<?php

//Prevent direct access
defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: SMS Notification
  Description: SMS notification for RISE CRM.
  Version: 1.0
  Requires at least: 2.8
  Author: SketchCode
  Author URL: https://codecanyon.net/user/sketchcode
 */

app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["setup"][] = array("name" => "sms_notifications", "url" => "sms");
    return $settings_menu;
});

app_hooks()->add_action('app_hook_post_notification', function ($notification_id) {
    $Sms_notifications_model = new \SMS\Models\Sms_notifications_model();

    if (get_sms_setting("enable_sms") && get_sms_setting("twilio_account_sid") && get_sms_setting("twilio_auth_token") && get_sms_setting("twilio_phone_number")) {
        $Sms_notifications_model->create_sms_notification($notification_id);
    }
});

//installation: install dependencies
register_installation_hook("SMS", function ($item_purchase_code) {
    include PLUGINPATH . "SMS/install/do_install.php";
});


//uninstallation: remove data from database
register_uninstallation_hook("SMS", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE `" . $dbprefix . "sms_notification_logs`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "sms_notification_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "sms_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "sms_templates`;";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='sms_item_purchase_code';";
    $db->query($sql_query);
});

//update plugin
use SMS\Controllers\Sms_Updates;

register_update_hook("SMS", function () {
    $update = new Sms_Updates();
    return $update->index();
});
