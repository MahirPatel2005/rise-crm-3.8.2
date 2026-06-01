<?php

use App\Controllers\Security_Controller;

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_banner_manager_setting')) {

    function get_banner_manager_setting($key = "") {
        $config = new Banner_Manager\Config\Banner_Manager();

        $setting_value = get_array_value($config->app_settings_array, $key);
        if ($setting_value !== NULL) {
            return $setting_value;
        } else {
            return "";
        }
    }

}

if (!function_exists('can_manage_banner_manager')) {

    function can_manage_banner_manager() {
        $banner_manager_users = get_banner_manager_setting("banner_manager_users");
        $banner_manager_users = explode(',', $banner_manager_users);
        $instance = new Security_Controller();

        if ($instance->login_user->is_admin || in_array($instance->login_user->id, $banner_manager_users)) {
            return true;
        }
    }

}

/**
 * link the css files 
 * 
 * @param array $array
 * @return print css links
 */
if (!function_exists('banner_manager_load_css')) {

    function banner_manager_load_css(array $array) {
        $version = get_setting("app_version");

        foreach ($array as $uri) {
            echo "<link rel='stylesheet' type='text/css' href='" . base_url(PLUGIN_URL_PATH . "Banner_Manager/$uri") . "?v=$version' />";
        }
    }

}

if (!function_exists('banner_manager_get_banner_source_url')) {

    function banner_manager_get_banner_source_url($banner_file = "") {
        if (!$banner_file) {
            return "";
        }

        try {
            $file = unserialize($banner_file);
            if (is_array($file)) {
                return get_source_url_of_file($file, get_banner_manager_setting("banner_manager_banner_file_path"), "thumbnail", false, false, true);
            }
        } catch (\Exception $ex) {
            
        }
    }

}
