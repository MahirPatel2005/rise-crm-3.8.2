<?php

/* Don't change or add any new config in this file */

namespace Banner_Manager\Config;

use CodeIgniter\Config\BaseConfig;
use Banner_Manager\Models\Banner_Manager_settings_model;

class Banner_Manager extends BaseConfig {

    public $app_settings_array = array(
        "banner_manager_banner_file_path" => PLUGIN_URL_PATH . "Banner_Manager/files/banner_files/"
    );

    public function __construct() {
        $banner_manager_settings_model = new Banner_Manager_settings_model();

        $settings = $banner_manager_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
