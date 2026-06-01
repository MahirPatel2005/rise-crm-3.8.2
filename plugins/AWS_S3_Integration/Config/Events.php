<?php

namespace AWS_S3_Integration\Config;

use CodeIgniter\Events\Events;
use AWS_S3_Integration\Models\AWS_S3_Integration_settings_model;

Events::on('pre_system', function () {
    helper("aws_s3_integration_general");

    $aws_s3_integration_settings_model = new AWS_S3_Integration_settings_model();
    if (!defined('PLUGIN_CUSTOM_STORAGE') &&
            $aws_s3_integration_settings_model->get_setting("integrate_aws_s3") &&
            $aws_s3_integration_settings_model->get_setting('aws_s3_authorized')) {
        define('PLUGIN_CUSTOM_STORAGE', TRUE); //define there has a custom storage enabled
    }
});
