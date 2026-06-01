<?php

/* Don't change or add any new config in this file */

namespace Jitsi_Integration\Config;

use CodeIgniter\Config\BaseConfig;
use Jitsi_Integration\Models\Jitsi_Integration_settings_model;

class Jitsi_Integration extends BaseConfig {

    public $app_settings_array = array();

    public function __construct() {
        $jitsi_integration_settings_model = new Jitsi_Integration_settings_model();

        $settings = $jitsi_integration_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
