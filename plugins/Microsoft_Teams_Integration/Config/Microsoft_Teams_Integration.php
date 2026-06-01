<?php

/* Don't change or add any new config in this file */

namespace Microsoft_Teams_Integration\Config;

use CodeIgniter\Config\BaseConfig;
use Microsoft_Teams_Integration\Models\Microsoft_Teams_Integration_settings_model;

class Microsoft_Teams_Integration extends BaseConfig {

    public $app_settings_array = array();

    public function __construct() {
        $microsoft_teams_integration_settings_model = new Microsoft_Teams_Integration_settings_model();

        $settings = $microsoft_teams_integration_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
