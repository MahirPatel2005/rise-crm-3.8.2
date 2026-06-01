<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Jitsi Integration
  Description: Create and manage Jitsi meetings with your team members and clients inside RISE CRM.
  Version: 1.0
  Requires at least: 2.9.2
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler
 */

use App\Controllers\Security_Controller;

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', 'jitsi_integration_left_menu');
app_hooks()->add_filter('app_filter_client_left_menu', 'jitsi_integration_left_menu');

if (!function_exists('jitsi_integration_left_menu')) {

    function jitsi_integration_left_menu($sidebar_menu) {
        if (!get_jitsi_integration_setting("integrate_jitsi")) {
            return $sidebar_menu;
        }

        $instance = new Security_Controller();
        if ($instance->login_user->user_type === "client" && !get_jitsi_integration_setting("client_can_access_meetings")) {
            return $sidebar_menu;
        }

        $sidebar_menu["jitsi_meetings"] = array(
            "name" => "jitsi_meetings",
            "url" => "jitsi_meetings",
            "class" => "video",
            "position" => 6,
            "badge" => jitsi_integration_count_upcoming_meetings(),
            "badge_class" => "bg-primary"
        );

        return $sidebar_menu;
    }

}

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["setup"][] = array("name" => "jitsi_integration", "url" => "jitsi_integration_settings");
    return $settings_menu;
});

//install dependencies
register_installation_hook("Jitsi_Integration", function ($item_purchase_code) {
    include PLUGINPATH . "Jitsi_Integration/install/do_install.php";
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Jitsi_Integration', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("jitsi_integration_settings"), app_lang("settings"))
    );

    if (get_jitsi_integration_setting("integrate_jitsi")) {
        $action_links_array[] = anchor(get_uri("jitsi_meetings"), app_lang("jitsi_integration_meetings"));
    }

    $action_links_array[] = anchor("https://rise.classiccompiler.com/documentation/jitsi_integration/", "Documentation", array("target" => "_blank"));

    return $action_links_array;
});

//update plugin
use Jitsi_Integration\Controllers\Jitsi_Integration_Updates;

register_update_hook("Jitsi_Integration", function () {
    $update = new Jitsi_Integration_Updates();
    return $update->index();
});

//uninstallation: remove data from database
register_uninstallation_hook("Jitsi_Integration", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "jitsi_integration_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "jitsi_meetings`;";
    $db->query($sql_query);
});
