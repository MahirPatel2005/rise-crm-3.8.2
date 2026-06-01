<?php

/* Don't change or add any new config in this file */

namespace Timecamp_Integration\Config;

use CodeIgniter\Config\BaseConfig;
use Timecamp_Integration\Models\Timecamp_Integration_settings_model;

class Timecamp_Integration extends BaseConfig {

    public $app_settings_array = array();

    public function __construct() {
        $timecamp_integration_settings_model = new Timecamp_Integration_settings_model();

        $settings = $timecamp_integration_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
