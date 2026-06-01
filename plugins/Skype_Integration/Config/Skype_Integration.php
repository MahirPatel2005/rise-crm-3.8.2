<?php

/* Don't change or add any new config in this file */

namespace Skype_Integration\Config;

use CodeIgniter\Config\BaseConfig;
use Skype_Integration\Models\Skype_Integration_settings_model;

class Skype_Integration extends BaseConfig {

    public $app_settings_array = array();

    public function __construct() {
        $skype_integration_settings_model = new Skype_Integration_settings_model();

        $settings = $skype_integration_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
