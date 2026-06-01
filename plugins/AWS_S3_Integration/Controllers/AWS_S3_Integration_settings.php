<?php

namespace AWS_S3_Integration\Controllers;

use App\Controllers\Security_Controller;
use AWS_S3_Integration\Libraries\AWS_S3_Integration;

class AWS_S3_Integration_settings extends Security_Controller {

    protected $AWS_S3_Integration_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->AWS_S3_Integration_settings_model = new \AWS_S3_Integration\Models\AWS_S3_Integration_settings_model();
    }

    function index() {
        $view_data["region_dropdown"] = get_aws_s3_region_dropdown();

        return $this->template->view("AWS_S3_Integration\Views\settings\index", $view_data);
    }

    function save_aws_s3_integration_settings() {
        $settings = array("integrate_aws_s3", "access_key_id", "secret_access_key", "bucket_name", "region");

        $integrate_aws_s3 = $this->request->getPost("integrate_aws_s3");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            //if user change credentials, flag it as unauthorized
            if (get_aws_s3_integration_setting('aws_s3_authorized') && ($setting == "access_key_id" || $setting == "secret_access_key" || $setting == "bucket_name" || $setting == "region") && $integrate_aws_s3 && get_aws_s3_integration_setting($setting) != $value) {
                $this->AWS_S3_Integration_settings_model->save_setting('aws_s3_authorized', "0");
            }

            $this->AWS_S3_Integration_settings_model->save_setting($setting, $value);
        }

        if ($integrate_aws_s3) {
            //authorize
            $AWS_S3_Integration = new AWS_S3_Integration();
            $authorization = $AWS_S3_Integration->authorize();

            if ($authorization === "authorized") {
                echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
            } else {
                echo json_encode(array("success" => false, 'message' => $authorization));
            }
        } else {
            echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
        }
    }

}
