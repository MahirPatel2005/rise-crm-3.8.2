<?php

namespace Banner_Manager\Controllers;

use App\Controllers\Security_Controller;

class Banner_Manager_settings extends Security_Controller {

    protected $Banner_Manager_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Banner_Manager_settings_model = new \Banner_Manager\Models\Banner_Manager_settings_model();
    }

    function index() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "is_admin" => 0))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $view_data['members_dropdown'] = json_encode($members_dropdown);

        return $this->template->rander("Banner_Manager\Views\settings\index", $view_data);
    }

    function save_banner_manager_settings() {
        $settings = array("banner_manager_users");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Banner_Manager_settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

}
