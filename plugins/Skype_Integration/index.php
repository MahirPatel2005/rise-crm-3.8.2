<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Skype Integration
  Description: Create and manage Skype meetings with your team members and clients inside RISE CRM.
  Version: 1.0
  Requires at least: 2.9.2
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler
 */

use App\Controllers\Security_Controller;

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', 'skype_integration_left_menu');
app_hooks()->add_filter('app_filter_client_left_menu', 'skype_integration_left_menu');

if (!function_exists('skype_integration_left_menu')) {

    function skype_integration_left_menu($sidebar_menu) {
        if (!(get_skype_integration_setting("integrate_skype") && get_skype_integration_setting('skype_authorized'))) {
            return $sidebar_menu;
        }

        $instance = new Security_Controller();
        if ($instance->login_user->user_type === "client" && !get_skype_integration_setting("client_can_access_meetings")) {
            return $sidebar_menu;
        }

        $sidebar_menu["skype_meetings"] = array(
            "name" => "skype_meetings",
            "url" => "skype_meetings",
            "class" => "video",
            "position" => 6,
            "badge" => skype_integration_count_upcoming_meetings(),
            "badge_class" => "bg-primary"
        );

        return $sidebar_menu;
    }

}

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["setup"][] = array("name" => "skype_integration", "url" => "skype_integration_settings");
    return $settings_menu;
});

//install dependencies
register_installation_hook("Skype_Integration", function ($item_purchase_code) {
    include PLUGINPATH . "Skype_Integration/install/do_install.php";
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Skype_Integration', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("skype_integration_settings"), app_lang("settings"))
    );

    if (get_skype_integration_setting("integrate_skype") && get_skype_integration_setting('skype_authorized')) {
        $action_links_array[] = anchor(get_uri("skype_meetings"), app_lang("skype_integration_meetings"));
    }

    return $action_links_array;
});

//update plugin
use Skype_Integration\Controllers\Skype_Integration_Updates;

register_update_hook("Skype_Integration", function () {
    $update = new Skype_Integration_Updates();
    return $update->index();
});

//uninstallation: remove data from database
register_uninstallation_hook("Skype_Integration", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "skype_integration_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "skype_meetings`;";
    $db->query($sql_query);
});
