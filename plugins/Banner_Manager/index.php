<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Banner Manager
  Description: Create and manage Banners with your team members and clients inside RISE CRM.
  Version: 1.0
  Requires at least: 3.0
  Author: codenplug
  Author URL: https://codecanyon.net/user/codenplug
 */

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', 'banner_manager_left_menu');

if (!function_exists('banner_manager_left_menu')) {

    function banner_manager_left_menu($sidebar_menu) {
        if (!can_manage_banner_manager()) {
            return $sidebar_menu;
        }

        $sidebar_menu["banner_manager"] = array(
            "name" => "banner_manager",
            "url" => "banner_manager",
            "class" => "cast",
            "position" => 16,
        );

        return $sidebar_menu;
    }

}

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["setup"][] = array("name" => "banner_manager", "url" => "banner_manager_settings");
    return $settings_menu;
});

//install dependencies
register_installation_hook("Banner_Manager", function ($item_purchase_code) {
    include PLUGINPATH . "Banner_Manager/install/do_install.php";
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Banner_Manager', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("banner_manager_settings"), app_lang("settings")),
        anchor(get_uri("banner_manager"), app_lang("banner_manager_banners")),
    );

    return $action_links_array;
});

//update plugin
use Banner_Manager\Controllers\Banner_Manager_Updates;

register_update_hook("Banner_Manager", function () {
    $update = new Banner_Manager_Updates();
    return $update->index();
});

//uninstallation: remove data from database
register_uninstallation_hook("Banner_Manager", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "banner_manager_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "banner_manager`;";
    $db->query($sql_query);
});

//show banners
use App\Controllers\Security_Controller;

app_hooks()->add_action('app_hook_dashboard_announcement_extension', function () {
    $instance = new Security_Controller(false);

    $is_client = false;
    if ($instance->login_user->user_type === "client") {
        $is_client = true;
    }

    $options = array(
        "user_id" => $instance->login_user->id,
        "team_ids" => $instance->login_user->team_ids,
        "is_client" => $is_client,
        "is_alert" => true,
    );

    //check if there has any banner id in the link
    //if has that, show only that banner
    if (isset($_GET["banner"]) && $_GET["banner"] && can_manage_banner_manager()) {
        $banner_id = $_GET["banner"];
        $options = array("id" => $banner_id);
    }

    $Banner_Manager_model = new \Banner_Manager\Models\Banner_Manager_model();
    $banners = $Banner_Manager_model->get_details($options)->getResult();
    $view_data["banners"] = $banners;

    echo view("Banner_Manager\Views\banner_manager\banners_alert", $view_data);
});
