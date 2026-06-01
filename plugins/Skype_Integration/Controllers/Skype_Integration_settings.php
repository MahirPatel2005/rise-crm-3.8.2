<?php

namespace Skype_Integration\Controllers;

use App\Controllers\Security_Controller;
use Skype_Integration\Libraries\Skype_Integration;

class Skype_Integration_settings extends Security_Controller {

    protected $Skype_Integration_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Skype_Integration_settings_model = new \Skype_Integration\Models\Skype_Integration_settings_model();
    }

    function index() {
        return $this->template->rander("Skype_Integration\Views\settings\index");
    }

    function other_settings() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "is_admin" => 0))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $view_data['members_dropdown'] = json_encode($members_dropdown);

        return $this->template->view("Skype_Integration\Views\settings\other_settings", $view_data);
    }

    function save_other_settings() {
        $settings = array(
            "skype_integration_users", "client_can_access_meetings"
        );

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Skype_Integration_settings_model->save_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function save_skype_integration_settings() {
        $settings = array("integrate_skype", "client_id", "client_secret");

        $integrate_skype = $this->request->getPost("integrate_skype");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            //if user change credentials, flag it as unauthorized
            if (get_skype_integration_setting('skype_authorized') && ($setting == "client_id" || $setting == "client_secret") && $integrate_skype && get_skype_integration_setting($setting) != $value) {
                $this->Skype_Integration_settings_model->save_setting('skype_authorized', "0");
            }

            $this->Skype_Integration_settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    //authorize
    function authorize() {
        $Skype_Integration = new Skype_Integration();
        $Skype_Integration->authorize();
    }

    //get access code and save
    function save_access_token() {
        if (!empty($_GET)) {
            $Skype_Integration = new Skype_Integration();
            $Skype_Integration->save_access_token(get_array_value($_GET, 'code'));
            app_redirect("skype_integration_settings");
        }
    }

}
