<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Recruitment Management
  Description: Recruitment Management for RISE CRM.
  Version: 1.0
  Requires at least: 3.3
  Author: SketchCode
  Author URL: https://codecanyon.net/user/sketchcode
 */

use App\Controllers\Security_Controller;

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
    $recruitment_submenu = array();
    $recruitment_submenu["recruitment_circulars"] = array("name" => "recruitment_circulars", "url" => "recruitment_management/circulars", "class" => "user-plus");
    $recruitment_submenu["recruitment_candidates"] = array("name" => "recruitment_candidates", "url" => "recruitment_candidates/index", "class" => "user");

    $sidebar_menu["recruitments"] = array(
        "name" => "recruitments",
        "url" => "recruitment",
        "class" => "user-plus",
        "position" => 6,
        "badge" => recruitment_count_active_circulars(),
        "badge_class" => "bg-primary me-4",
        "submenu" => $recruitment_submenu
    );

    return $sidebar_menu;
});

//add setting link and recruitments link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Recruitment_management', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("recruitment_settings"), app_lang("settings")),
        anchor(get_uri("recruitment_management/circulars"), app_lang("recruitments"))
    );

    return $action_links_array;
});

//admin setting menu
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["plugins"][] = array("name" => "recruitments", "url" => "recruitment_settings");
    return $settings_menu;
});

//installation: install dependencies
register_installation_hook("Recruitment_management", function ($item_purchase_code) {
    include PLUGINPATH . "Recruitment_management/install/do_install.php";
});

//uninstallation: remove data from database
register_uninstallation_hook("Recruitment_management", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_application_forms`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_hiring_stages`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_event_types`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_job_types`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_job_positions`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_departments`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_locations`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_jobs`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_job_templates`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_candidates`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_candidate_events`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "recruitment_candidate_notes`;";
    $db->query($sql_query);
});

//update plugin
use Recruitment_management\Controllers\Recruitment_management_Updates;

register_update_hook("Recruitment_management", function () {
    $update = new Recruitment_management_Updates();
    return $update->index();
});
