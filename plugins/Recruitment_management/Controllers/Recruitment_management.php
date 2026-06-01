<?php

namespace Recruitment_management\Controllers;

class Recruitment_management extends \App\Controllers\Security_Controller {

    protected $Recruitment_jobs_model;
    protected $Recruitment_job_types_model;
    protected $Recruitment_job_positions_model;
    protected $Recruitment_departments_model;
    protected $Recruitment_locations_model;
    protected $Recruitment_hiring_stages_model;
    protected $Recruitment_job_templates_model;

    function __construct() {
        parent::__construct();
        $this->Recruitment_jobs_model = new \Recruitment_management\Models\Recruitment_jobs_model();
        $this->Recruitment_job_types_model = new \Recruitment_management\Models\Recruitment_job_types_model();
        $this->Recruitment_job_positions_model = new \Recruitment_management\Models\Recruitment_job_positions_model();
        $this->Recruitment_departments_model = new \Recruitment_management\Models\Recruitment_departments_model();
        $this->Recruitment_locations_model = new \Recruitment_management\Models\Recruitment_locations_model();
        $this->Recruitment_hiring_stages_model = new \Recruitment_management\Models\Recruitment_hiring_stages_model();
        $this->Recruitment_job_templates_model = new \Recruitment_management\Models\Recruitment_job_templates_model();
    }

    /* check circular permission */

    protected function can_manage_circulars() {
        if ($this->login_user->is_admin) {
            return true;
        }
    }

    private function can_view_circulars($circular_id = "") {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_circulars()) {
                return true;
            } else {
                if ($circular_id) {
                    //user has permission to view only assigned circulars
                    $circular_info = $this->Recruitment_jobs_model->get_one($circular_id);
                    $recruiters_array = explode(',', $circular_info->recruiters);
                    if (in_array($this->login_user->id, $recruiters_array)) {
                        return true;
                    }
                }
            }
        }
    }

    /* get dropdown lists */

    private function _get_job_types_dropdown_list() {
        $job_types = $this->Recruitment_job_types_model->get_details(array("deleted" => 0))->getResult();
        $job_type_dropdown = array(array("id" => "", "text" => "- " . app_lang("recruitment_job_type") . " -"));

        foreach ($job_types as $job_type) {
            $job_type_dropdown[] = array("id" => $job_type->id, "text" => $job_type->title);
        }
        return $job_type_dropdown;
    }

    private function _get_job_positions_dropdown_list() {
        $job_positions = $this->Recruitment_job_positions_model->get_details(array("deleted" => 0))->getResult();
        $job_position_dropdown = array(array("id" => "", "text" => "- " . app_lang("recruitment_job_position") . " -"));

        foreach ($job_positions as $job_position) {
            $job_position_dropdown[] = array("id" => $job_position->id, "text" => $job_position->title);
        }
        return $job_position_dropdown;
    }

    private function _get_departments_dropdown_list() {
        $departments = $this->Recruitment_departments_model->get_details(array("deleted" => 0))->getResult();
        $department_dropdown = array(array("id" => "", "text" => "- " . app_lang("recruitment_department") . " -"));

        foreach ($departments as $department) {
            $department_dropdown[] = array("id" => $department->id, "text" => $department->title);
        }
        return $department_dropdown;
    }

    private function _get_locations_dropdown_list() {
        $locations = $this->Recruitment_locations_model->get_details(array("deleted" => 0))->getResult();
        $location_dropdown = array(array("id" => "", "text" => "- " . app_lang("recruitment_location") . " -"));

        foreach ($locations as $location) {
            $location_dropdown[] = array("id" => $location->id, "text" => $location->address);
        }
        return $location_dropdown;
    }

    /* load circulars view */

    function circulars() {
        return $this->template->rander("Recruitment_management\Views\jobs\index");
    }

    /* load circular add/edit modal */

    function job_modal_form() {
        $id = $this->request->getPost('id');
        $model_info = $this->Recruitment_jobs_model->get_one($id);

        if ($id) {
            if (!$this->can_view_circulars($id)) {
                app_redirect("forbidden");
            }
        }

        $view_data['job_types_dropdown'] = $this->_get_job_types_dropdown_list();
        $view_data['job_positions_dropdown'] = $this->_get_job_positions_dropdown_list();
        $view_data['departments_dropdown'] = $this->_get_departments_dropdown_list();
        $view_data['locations_dropdown'] = $this->_get_locations_dropdown_list();

        //prepare assign to list
        $users = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->getResult();
        $recruiters = array();
        foreach ($users as $user) {
            $recruiters[] = array("id" => $user->id, "text" => $user->first_name . " " . $user->last_name);
        }

        $view_data['recruiters_dropdown'] = json_encode($recruiters);

        $view_data['model_info'] = $model_info;

        return $this->template->view('Recruitment_management\Views\jobs\modal_form', $view_data);
    }

    /* add or edit a circular */

    function save_job() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "job_title" => "required"
        ));

        $id = $this->request->getPost('id');

        if ($id) {
            if (!$this->can_view_circulars($id)) {
                app_redirect("forbidden");
            }
        }

        $data = array(
            "start_date" => $this->request->getPost('start_date'),
            "deadline" => $this->request->getPost('deadline'),
            "job_title" => $this->request->getPost('job_title'),
            "status" => $this->request->getPost('status'),
            "job_type_id" => $this->request->getPost('job_type_id'),
            "job_position_id" => $this->request->getPost('job_position_id'),
            "department_id" => $this->request->getPost('department_id'),
            "location_id" => $this->request->getPost('location_id'),
            "description" => $this->request->getPost('description'),
            "quantity_to_be_required" => $this->request->getPost('quantity_to_be_required'),
            "salary" => $this->request->getPost('salary'),
            "recruiters" => $this->request->getPost('recruiters')
        );

        if (!$id) {
            //save random code for new circular
            $data["public_key"] = make_random_string();

            $data["created_at"] = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;

            //add default template
            if (get_recruitment_setting("default_recruitment_job_circular_template")) {
                $data["content"] = $this->Recruitment_job_templates_model->get_one(get_recruitment_setting("default_recruitment_job_circular_template"))->template;
            }
        }

        $save_id = $this->Recruitment_jobs_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_job_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete a circular */

    function delete_job() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($id) {
            if (!$this->can_view_circulars($id)) {
                app_redirect("forbidden");
            }
        }

        if ($this->Recruitment_jobs_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /* list of circular, prepared for datatable  */

    function job_list_data() {
        //only admin can see all circulars, other team mebers can see only their the circular where they are recruiters.
        $options = array();
        if (!$this->can_manage_circulars()) {
            $options["user_id"] = $this->login_user->id;
        }

        $list_data = $this->Recruitment_jobs_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_job_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of circular list  table */

    function _job_row_data($id) {
        $data = $this->Recruitment_jobs_model->get_details(array("id" => $id))->getRow();
        return $this->_make_job_row($data);
    }

    /* prepare a row of circular list table */

    function _make_job_row($data) {
        $title = anchor(get_uri("recruitment_management/view_circular/" . $data->id), $data->job_title);

        $deadline = "-";
        if ($data->deadline && is_date_exists($data->deadline)) {
            $deadline = format_to_date($data->deadline, false);
            if (get_my_local_time("Y-m-d") > $data->deadline) {
                $deadline = "<span class='text-danger'>" . $deadline . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->deadline && $data->status != "inactive") {
                $deadline = "<span class='text-warning'>" . $deadline . "</span> ";
            }
        }

        $status_class = "bg-orange";
        if ($data->status == "active") {
            $status_class = "bg-success";
        } else if ($data->status == "inactive") {
            $status_class = "bg-danger";
        }

        $job_status = "<span class='badge $status_class large'>" . app_lang($data->status) . "</span> ";

        $actions = anchor(get_uri("recruitment_circulars/public_preview/" . $data->id . "/" . $data->public_key), "<i data-feather='external-link' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_circular') . " " . app_lang("url"), "target" => "_blank"))
                . modal_anchor(get_uri("recruitment_management/job_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_job'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_job'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_management/delete_job"), "data-action" => "delete-confirmation"));

        return array(
            $title,
            $data->start_date,
            $deadline,
            $data->job_type_title,
            $data->job_position_title,
            $data->department_title,
            $data->quantity_to_be_required,
            $job_status,
            $actions
        );
    }

    /* load circular details view */

    function view_circular($circular_id = 0) {
        if (!$this->can_view_circulars($circular_id)) {
            app_redirect("forbidden");
        }
        validate_numeric_value($circular_id);

        if ($circular_id) {
            $view_data = get_recruitment_circular_making_data($circular_id);
            if ($view_data) {
                $view_data['circular_status_label'] = $this->_get_circular_status_label($view_data["circular_info"]);
                $view_data['circular_status'] = $this->_get_circular_status_label($view_data["circular_info"], false);

                $circular_info = $this->Recruitment_jobs_model->get_details(array("id" => $circular_id))->getRow();

                $view_data["circular_info"] = $circular_info;
                $view_data["job_id"] = $circular_id;

                $view_data["hiring_stage_info"] = $this->Recruitment_hiring_stages_model->get_candidate_statistics($circular_id);
                $view_data["hiring_stage_columns"] = $this->Recruitment_hiring_stages_model->get_details()->getResult();

                $view_data['recruiters'] = $this->_get_recruiters($circular_info->recruiters_list);

                return $this->template->rander("Recruitment_management\Views\jobs\\view", $view_data);
            } else {
                show_404();
            }
        }
    }

    /* get recruiters for a circular */

    private function _get_recruiters($recruiter_list) {
        $recruiters = "";
        if ($recruiter_list) {

            $recruiters_array = explode(",", $recruiter_list);
            foreach ($recruiters_array as $recruiter) {
                $recruiter_parts = explode("--::--", $recruiter);

                $recruiter_name = get_array_value($recruiter_parts, 1);
                $image_url = get_avatar(get_array_value($recruiter_parts, 2));

                $collaboratr_image = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span>";

                $recruiters .= "<span data-bs-toggle='tooltip' title='$recruiter_name'>$collaboratr_image</span>";
            }
        }
        return $recruiters;
    }

    /* circular editor */

    function editor($circular_id = 0) {
        validate_numeric_value($circular_id);
        $view_data['circular_info'] = $this->Recruitment_jobs_model->get_details(array("id" => $circular_id))->getRow();
        return $this->template->view("Recruitment_management\Views\jobs\\job_editor", $view_data);
    }

    /* save circular for public view */

    function save_view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost("id");

        $circular_data = array(
            "content" => decode_ajax_post_data($this->request->getPost('view'))
        );

        $this->Recruitment_jobs_model->ci_save($circular_data, $id);

        echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
    }

    /* view html is accessable to client only. */

    function preview($circular_id = 0, $show_close_preview = false, $is_editor_preview = false) {
        validate_numeric_value($circular_id);

        $view_data = array();

        if ($circular_id) {

            $circular_data = get_recruitment_circular_making_data($circular_id);

            //get the label of the circular
            $circular_info = get_array_value($circular_data, "circular_info");
            $circular_data['circular_status_label'] = $this->_get_circular_status_label($circular_info);

            $view_data['recruitment_circular_preview'] = prepare_recruitment_circular_view($circular_data);

            //show a back button
            $view_data['show_close_preview'] = $show_close_preview && $this->login_user->user_type === "staff" ? true : false;

            $view_data['circular_id'] = $circular_id;
            $view_data['circular_info'] = $circular_info;

            if ($is_editor_preview) {
                $view_data["is_editor_preview"] = clean_data($is_editor_preview);
                return $this->template->view("Recruitment_management\Views\jobs\\circular_preview", $view_data);
            } else {
                return $this->template->rander("Recruitment_management\Views\jobs\\circular_preview", $view_data);
            }
        } else {
            show_404();
        }
    }

    /* prepare circular status label  */

    private function _get_circular_status_label($circular_info, $return_html = true) {
        $circular_status_class = "bg-secondary";

        if ($circular_info->status == "draft") {
            $circular_status_class = "bg-secondary";
        } else if ($circular_info->status == "active") {
            $circular_status_class = "bg-success";
        } else if ($circular_info->status == "inactive") {
            $circular_status_class = "bg-danger";
        }

        $circular_status = "<span class='mt0 badge $circular_status_class large'>" . app_lang($circular_info->status) . "</span>";
        if ($return_html) {
            return $circular_status;
        } else {
            return $circular_info->status;
        }
    }

    /* update circular status */

    function update_circular_status($circular_id = 0, $status = "") {
        validate_numeric_value($circular_id);

        if ($circular_id) {
            if (!$this->can_view_circulars($circular_id)) {
                app_redirect("forbidden");
            }

            $data = array("status" => $status);
            $this->Recruitment_jobs_model->ci_save($data, $circular_id);
        }
    }

    /* candidates view inside a circular */

    function candidates($circular_id = 0) {
        $view_data["circular_id"] = $circular_id;
        return $this->template->view("Recruitment_management\Views\jobs\\candidates", $view_data);
    }

}

/* End of file Recruitment_management.php */
/* Location: ./plugins/Recruitment_management/Controllers/Recruitment_management.php */