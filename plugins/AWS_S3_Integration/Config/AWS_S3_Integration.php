<?php

/* Don't change or add any new config in this file */

namespace AWS_S3_Integration\Config;

use CodeIgniter\Config\BaseConfig;
use AWS_S3_Integration\Models\AWS_S3_Integration_settings_model;

class AWS_S3_Integration extends BaseConfig {

    public $app_settings_array = array(
        "signed_url_validity" => "+300 minutes"
    );

    public function __construct() {
        $aws_s3_integration_settings_model = new AWS_S3_Integration_settings_model();

        $settings = $aws_s3_integration_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
