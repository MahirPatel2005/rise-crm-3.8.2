<?php

namespace SMS\Models;

class Sms_settings_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sms_settings';
        parent::__construct($this->table);
    }

    function get_sms_setting($setting_name) {
        $result = $this->db_builder->getWhere(array('setting_name' => $setting_name), 1);
        if (count($result->getResult()) == 1) {
            return $result->getRow()->setting_value;
        }
    }

    function save_sms_setting($setting_name, $setting_value, $type = "app") {
        $fields = array(
            'setting_name' => $setting_name,
            'setting_value' => $setting_value
        );

        $exists = $this->get_sms_setting($setting_name);
        if ($exists === NULL) {
            $fields["type"] = $type; //type can't be updated

            return $this->db_builder->insert($fields);
        } else {
            $this->db_builder->where('setting_name', $setting_name);
            $this->db_builder->update($fields);
        }
    }

}
