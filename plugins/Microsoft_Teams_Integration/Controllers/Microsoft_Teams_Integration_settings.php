<?php

namespace Microsoft_Teams_Integration\Controllers;

use App\Controllers\Security_Controller;
use Microsoft_Teams_Integration\Libraries\Microsoft_Teams_Integration;

class Microsoft_Teams_Integration_settings extends Security_Controller {

    protected $Microsoft_Teams_Integration_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Microsoft_Teams_Integration_settings_model = new \Microsoft_Teams_Integration\Models\Microsoft_Teams_Integration_settings_model();
    }

    function index() {
        return $this->template->rander("Microsoft_Teams_Integration\Views\settings\index");
    }

    function other_settings() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "is_admin" => 0))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $view_data['members_dropdown'] = json_encode($members_dropdown);

        return $this->template->view("Microsoft_Teams_Integration\Views\settings\other_settings", $view_data);
    }

    function save_other_settings() {
        $settings = array(
            "microsoft_teams_integration_users", "client_can_access_meetings"
        );

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Microsoft_Teams_Integration_settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function save_microsoft_teams_integration_settings() {
        $settings = array("integrate_microsoft_teams", "client_id", "client_secret");

        $integrate_microsoft_teams = $this->request->getPost("integrate_microsoft_teams");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            //if user change credentials, flag it as unauthorized
            if (get_microsoft_teams_integration_setting('microsoft_teams_authorized') && ($setting == "client_id" || $setting == "client_secret") && $integrate_microsoft_teams && get_microsoft_teams_integration_setting($setting) != $value) {
                $this->Microsoft_Teams_Integration_settings_model->save_setting('microsoft_teams_authorized', "0");
            }

            $this->Microsoft_Teams_Integration_settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    //authorize
    function authorize() {
        $Microsoft_Teams_Integration = new Microsoft_Teams_Integration();
        $Microsoft_Teams_Integration->authorize();
    }

    //get access code and save
    function save_access_token() {
        if (!empty($_GET)) {
            $Microsoft_Teams_Integration = new Microsoft_Teams_Integration();
            $Microsoft_Teams_Integration->save_access_token(get_array_value($_GET, 'code'));
            app_redirect("microsoft_teams_integration_settings");
        }
    }

}
