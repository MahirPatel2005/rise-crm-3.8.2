<?php

namespace Recruitment_management\Controllers;

class Recruitment_settings extends \App\Controllers\Security_Controller {

    protected $Recruitment_application_forms_model;
    protected $Recruitment_hiring_stages_model;
    protected $Recruitment_event_types_model;
    protected $Recruitment_job_types_model;
    protected $Recruitment_job_positions_model;
    protected $Recruitment_departments_model;
    protected $Recruitment_locations_model;
    protected $Recruitment_job_templates_model;
    protected $Recruitment_settings_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Recruitment_application_forms_model = new \Recruitment_management\Models\Recruitment_application_forms_model();
        $this->Recruitment_hiring_stages_model = new \Recruitment_management\Models\Recruitment_hiring_stages_model();
        $this->Recruitment_event_types_model = new \Recruitment_management\Models\Recruitment_event_types_model();
        $this->Recruitment_job_types_model = new \Recruitment_management\Models\Recruitment_job_types_model();
        $this->Recruitment_job_positions_model = new \Recruitment_management\Models\Recruitment_job_positions_model();
        $this->Recruitment_departments_model = new \Recruitment_management\Models\Recruitment_departments_model();
        $this->Recruitment_locations_model = new \Recruitment_management\Models\Recruitment_locations_model();
        $this->Recruitment_job_templates_model = new \Recruitment_management\Models\Recruitment_job_templates_model();
        $this->Recruitment_settings_model = new \Recruitment_management\Models\Recruitment_settings_model();
    }

    /* recruitment settings view */

    function index() {
        $view_data['job_templates_dropdown'] = array("" => "-") + $this->Recruitment_job_templates_model->get_dropdown_list(array("title"), "id");

        return $this->template->rander("Recruitment_management\Views\settings\index", $view_data);
    }

    /* save recruitment settings */

    function save_recruitment_settings() {
        $settings = array("recruitment_job_circular_color", "default_recruitment_job_circular_template");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Recruitment_settings_model->save_recruitment_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    /* application settings */

    function application_form() {
        return $this->template->view("Recruitment_management\Views\settings\application_form");
    }

    function application_form_modal_form() {

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_application_forms_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\application_form_modal_form', $view_data);
    }

    function application_form_save() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "required" => $this->request->getPost('required')
        );
        $save_id = $this->Recruitment_application_forms_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_application_form_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function application_form_list_data() {
        $list_data = $this->Recruitment_application_forms_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_application_form_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _application_form_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_application_forms_model->get_details($options)->getRow();
        return $this->_application_form_make_row($data);
    }

    private function _application_form_make_row($data) {
        $yes = "<i data-feather='check-circle' class='icon-16'></i>";
        $no = "<i data-feather='check-circle' class='icon-16' style='opacity:0.2'></i>";

        $action = "";
        if (!$data->is_default) {
            $action = modal_anchor(get_uri("recruitment_settings/application_form_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_application_form'), "data-post-id" => $data->id));
        }

        return array(
            $data->sort,
            "<div class='pt10 pb10 field-row'  data-id='$data->id'><div class='float-start text-off me-2'><i data-feather='menu' class='icon-16'></i> </div>" . $data->title . '</div>',
            $data->required ? $yes : $no,
            $action
        );
    }

    /* recruitment hiring stage setting */

    function hiring_stage() {
        return $this->template->view("Recruitment_management\Views\settings\hiring_stage");
    }

    function hiring_stage_modal_form() {

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_hiring_stages_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\hiring_stage_modal_form', $view_data);
    }

    function hiring_stage_save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title')
        );
        $save_id = $this->Recruitment_hiring_stages_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_hiring_stage_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function hiring_stage_delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_hiring_stages_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_hiring_stage_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_hiring_stages_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function hiring_stage_list_data() {
        $list_data = $this->Recruitment_hiring_stages_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_hiring_stage_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _hiring_stage_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_hiring_stages_model->get_details($options)->getRow();
        return $this->_hiring_stage_make_row($data);
    }

    private function _hiring_stage_make_row($data) {
        $action = "";

        if (!$data->key_name) {
            $action = modal_anchor(get_uri("recruitment_settings/hiring_stage_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_hiring_stage'), "data-post-id" => $data->id))
                    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_hiring_stage'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_settings/hiring_stage_delete"), "data-action" => "delete"));
        }

        return array(
            $data->sort,
            "<div class='pt10 pb10 field-row'  data-id='$data->id'><div class='float-start move-icon'><i data-feather='menu' class='icon-16'></i> </div>" . $data->title . '</div>',
            $action
        );
    }

    //update the sort value for the fields
    function update_hiring_stage_field_sort_values($id = 0) {

        $sort_values = $this->request->getPost("sort_values");
        if ($sort_values) {

            //extract the values from the comma separated string
            $sort_array = explode(",", $sort_values);

            //update the value in db
            foreach ($sort_array as $value) {
                $sort_item = explode("-", $value); //extract id and sort value

                $id = get_array_value($sort_item, 0);
                $sort = get_array_value($sort_item, 1);

                $data = array("sort" => $sort);
                $this->Recruitment_hiring_stages_model->ci_save($data, $id);
            }
        }
    }

    /* recruitment event type settings */

    function event_type() {
        return $this->template->view("Recruitment_management\Views\settings\\event_type");
    }

    function event_type_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_event_types_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\event_type_modal_form', $view_data);
    }

    function save_event_type() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title')
        );
        $save_id = $this->Recruitment_event_types_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_event_type_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete_event_type() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_event_types_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_event_type_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_event_types_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function event_type_list_data() {
        $list_data = $this->Recruitment_event_types_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_event_type_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _event_type_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_event_types_model->get_details($options)->getRow();
        return $this->_event_type_make_row($data);
    }

    private function _event_type_make_row($data) {
        return array(
            $data->title,
            modal_anchor(get_uri("recruitment_settings/event_type_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_event_type'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_event_type'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_settings/delete_event_type"), "data-action" => "delete"))
        );
    }

    /* recruitment job type setting */

    function job_type() {
        return $this->template->view("Recruitment_management\Views\settings\\job_type");
    }

    function job_type_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_job_types_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\job_type_modal_form', $view_data);
    }

    function save_job_type() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
        );
        $save_id = $this->Recruitment_job_types_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_job_type_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete_job_type() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_job_types_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_job_type_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_job_types_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function job_type_list_data() {
        $list_data = $this->Recruitment_job_types_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_job_type_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _job_type_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_job_types_model->get_details($options)->getRow();
        return $this->_job_type_make_row($data);
    }

    private function _job_type_make_row($data) {
        return array(
            $data->title,
            $data->description,
            modal_anchor(get_uri("recruitment_settings/job_type_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_job_type'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_job_type'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_settings/delete_job_type"), "data-action" => "delete"))
        );
    }

    /* recruitmnet job position setting */

    function job_position() {
        return $this->template->view("Recruitment_management\Views\settings\\job_position");
    }

    function job_position_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_job_positions_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\job_position_modal_form', $view_data);
    }

    function save_job_position() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title')
        );
        $save_id = $this->Recruitment_job_positions_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_job_position_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete_job_position() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_job_positions_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_job_position_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_job_positions_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function job_position_list_data() {
        $list_data = $this->Recruitment_job_positions_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_job_position_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _job_position_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_job_positions_model->get_details($options)->getRow();
        return $this->_job_position_make_row($data);
    }

    private function _job_position_make_row($data) {
        return array(
            $data->title,
            modal_anchor(get_uri("recruitment_settings/job_position_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_job_position'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_job_position'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_settings/delete_job_position"), "data-action" => "delete"))
        );
    }

    /* recruitment department setting */

    function department() {
        return $this->template->view("Recruitment_management\Views\settings\\department");
    }

    function department_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_departments_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\department_modal_form', $view_data);
    }

    function save_department() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title')
        );
        $save_id = $this->Recruitment_departments_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_department_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete_department() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_departments_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_department_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_departments_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function department_list_data() {
        $list_data = $this->Recruitment_departments_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_department_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _department_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_departments_model->get_details($options)->getRow();
        return $this->_department_make_row($data);
    }

    private function _department_make_row($data) {
        return array(
            $data->title,
            modal_anchor(get_uri("recruitment_settings/department_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_department'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_department'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_settings/delete_department"), "data-action" => "delete"))
        );
    }

    /* recruitment location setting */

    function location() {
        return $this->template->view("Recruitment_management\Views\settings\\location");
    }

    function location_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_locations_model->get_one($this->request->getPost('id'));
        return $this->template->view('Recruitment_management\Views\settings\location_modal_form', $view_data);
    }

    function save_location() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "address" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "address" => $this->request->getPost('address')
        );
        $save_id = $this->Recruitment_locations_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_location_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete_location() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_locations_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_location_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_locations_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function location_list_data() {
        $list_data = $this->Recruitment_locations_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_location_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _location_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_locations_model->get_details($options)->getRow();
        return $this->_location_make_row($data);
    }

    private function _location_make_row($data) {
        return array(
            $data->address,
            modal_anchor(get_uri("recruitment_settings/location_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_location'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_location'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_settings/delete_location"), "data-action" => "delete"))
        );
    }

}

/* End of file Recruitment_settings.php */
/* Location: ./plugins/Recruitment_management/Controllers/Recruitment_settings.php */