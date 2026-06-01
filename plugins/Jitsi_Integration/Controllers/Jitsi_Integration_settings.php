<?php

namespace Jitsi_Integration\Controllers;

use App\Controllers\Security_Controller;

class Jitsi_Integration_settings extends Security_Controller {

    protected $Jitsi_Integration_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Jitsi_Integration_settings_model = new \Jitsi_Integration\Models\Jitsi_Integration_settings_model();
    }

    function index() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "is_admin" => 0))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $view_data['members_dropdown'] = json_encode($members_dropdown);

        return $this->template->rander("Jitsi_Integration\Views\settings\index", $view_data);
    }

    function save() {
        $settings = array("integrate_jitsi", "jitsi_integration_users", "client_can_access_meetings");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Jitsi_Integration_settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

}
