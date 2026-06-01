<?php

/* Don't change or add any new config in this file */

namespace Auto_Attendance\Config;

use CodeIgniter\Config\BaseConfig;
use Auto_Attendance\Models\Auto_Attendance_settings_model;

class Auto_Attendance extends BaseConfig {

    public $app_settings_array = array();

    public function __construct() {
        $auto_attendance_settings_model = new Auto_Attendance_settings_model();

        $settings = $auto_attendance_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
