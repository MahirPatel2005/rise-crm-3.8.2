<?php

namespace Banner_Manager\Controllers;

use App\Controllers\Security_Controller;

class Banner_Manager extends Security_Controller {

    protected $Banner_Manager_model;

    function __construct() {
        parent::__construct();
        $this->Banner_Manager_model = new \Banner_Manager\Models\Banner_Manager_model();
    }

    private function can_manage_banner_manager() {
        if (!can_manage_banner_manager()) {
            app_redirect("forbidden");
        }
    }

    function index() {
        $this->can_manage_banner_manager();
        return $this->template->rander('Banner_Manager\Views\banner_manager\index');
    }

    function modal_form() {
        $this->can_manage_banner_manager();
        $id = $this->request->getPost("id");
        $model_info = $this->Banner_Manager_model->get_one($id);

        $view_data['members_and_teams_dropdown'] = json_encode(get_team_members_and_teams_select2_data_list());
        $view_data['clients_dropdown'] = $this->get_client_contacts_dropdown();
        $view_data['model_info'] = $model_info;

        return $this->template->view('Banner_Manager\Views\banner_manager\modal_form', $view_data);
    }

    private function get_client_contacts_dropdown() {
        $contacts_dropdown = array();

        $contacts = $this->Banner_Manager_model->get_client_contacts_list()->getResult();

        foreach ($contacts as $contact) {
            $contact_name = $contact->first_name . " " . $contact->last_name . " - " . app_lang("client") . ": " . $contact->company_name . "";
            $contacts_dropdown[] = array("id" => "contact:" . $contact->id, "text" => $contact_name);
        }

        return json_encode($contacts_dropdown);
    }

    /* list data of banners */

    function list_data() {
        $this->can_manage_banner_manager();
        $list_data = $this->Banner_Manager_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //prepare a row of banners list table
    private function _make_row($data) {
        $image_url = get_avatar($data->created_by_avatar);
        $user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->created_by_name";

        $row_data = array(
            $data->title,
            get_team_member_profile_link($data->created_by, $user),
            $data->start_date,
            format_to_date($data->start_date, false),
            $data->end_date,
            format_to_date($data->end_date, false),
            anchor(get_uri("dashboard") . "?banner=" . $data->id, app_lang("preview") . "<i data-feather='external-link' class='icon-16 ml10'></i>", array("target" => "_blank")),
            modal_anchor(get_uri("banner_manager/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('banner_manager_edit_banner'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('banner_manager_delete_banner'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("banner_manager/delete"), "data-action" => "delete-confirmation"))
        );

        return $row_data;
    }

    /* insert/update a banner */

    function save() {
        $this->can_manage_banner_manager();
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
            "start_date" => "required",
            "end_date" => "required"
        ));

        $id = $this->request->getPost('id');

        //prepare share with data
        $share_with_team_members = $this->request->getPost('share_with_team_members');
        if ($share_with_team_members == "specific") {
            $share_with_team_members = $this->request->getPost('share_with_specific_team_members');
        }
        $share_with_client_contacts = $this->request->getPost('share_with_client_contacts');
        if ($share_with_client_contacts == "specific") {
            $share_with_client_contacts = $this->request->getPost('share_with_specific_client_contacts');
        }

        $data = array(
            "title" => $this->request->getPost('title'),
            "start_date" => $this->request->getPost('start_date'),
            "end_date" => $this->request->getPost('end_date'),
            "share_with_team_members" => $share_with_team_members,
            "share_with_client_contacts" => $share_with_client_contacts,
        );

        $data = clean_data($data);

        //save user_id only on insert and it will not be editable
        if (!$id) {
            $data["created_by"] = $this->login_user->id;
            $data["read_by"] = 0; //set default value
        }

        //save file
        $banner_info = $this->Banner_Manager_model->get_one($id);
        $files_data = move_files_from_temp_dir_to_permanent_dir(get_banner_manager_setting("banner_manager_banner_file_path"), "banner");
        $unserialize_files_data = unserialize($files_data);
        $banner = get_array_value($unserialize_files_data, 0);
        if ($banner) {
            if ($id && $banner_info->banner) {
                //delete old file if exists
                $this->delete_banner_file($banner_info->banner);
            }

            $data["banner"] = serialize($banner);
        }

        //show error if there is no banner file
        if (!$id && !$banner) {
            echo json_encode(array("success" => false, 'message' => app_lang('banner_manager_please_upload_a_valid_image_file')));
            exit();
        }

        $save_id = $this->Banner_Manager_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    private function delete_banner_file($banner_file) {
        try {
            $banner_file = unserialize($banner_file);
        } catch (\Exception $ex) {
            echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
            exit();
        }

        delete_app_files(get_banner_manager_setting("banner_manager_banner_file_path"), array($banner_file));
    }

    /* permanently delete a banner */

    function delete() {
        $this->can_manage_banner_manager();
        $id = $this->request->getPost('id');

        $banner_info = $this->Banner_Manager_model->get_one($id);

        if ($this->Banner_Manager_model->delete($id)) {
            $this->delete_banner_file($banner_info->banner);
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /* return a row of banners list table */

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Banner_Manager_model->get_details($options)->getRow();

        return $this->_make_row($data);
    }

    /* upload a post file */

    function upload_file() {
        $this->can_manage_banner_manager();
        upload_file_to_temp();
    }

    /* check valid file for banner */

    function validate_banner_file() {
        $this->can_manage_banner_manager();
        $file_name = $this->request->getPost("file_name");
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if (is_image_file($file_name)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('banner_manager_please_upload_a_valid_image_file')));
        }
    }

    //mark the banner as read for loged in user
    function mark_as_read($id) {
        $this->Banner_Manager_model->mark_as_read($id, $this->login_user->id);
        echo json_encode(array("success" => true));
    }

}
