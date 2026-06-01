<?php

namespace Auto_Attendance\Controllers;

use App\Controllers\Security_Controller;

class Auto_Attendance_settings extends Security_Controller {

    protected $Auto_Attendance_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Auto_Attendance_settings_model = new \Auto_Attendance\Models\Auto_Attendance_settings_model();
    }

    function index() {
        return $this->template->rander("Auto_Attendance\Views\settings\index");
    }

    function save() {
        $settings = array("auto_clock_in_on_signin", "auto_clock_out_on_signout", "auto_clock_out_after");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Auto_Attendance_settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

}
