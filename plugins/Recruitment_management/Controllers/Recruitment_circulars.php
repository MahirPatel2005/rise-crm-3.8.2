<?php

namespace Recruitment_management\Controllers;

class Recruitment_circulars extends \App\Controllers\App_Controller {

    protected $Recruitment_jobs_model;
    protected $Recruitment_candidates_model;

    function __construct() {
        parent::__construct();
        $this->Recruitment_jobs_model = new \Recruitment_management\Models\Recruitment_jobs_model();
        $this->Recruitment_candidates_model = new \Recruitment_management\Models\Recruitment_candidates_model();
    }

    /* recruitment circular public page */

    function index() {
        $view_data['topbar'] = "includes/public/topbar";
        $view_data['left_menu'] = false;

        $view_data["recruitment_circulars"] = $this->Recruitment_jobs_model->get_details(array("status" => "active", "deleted" => 0))->getResult();
        return $this->template->rander("Recruitment_management\Views\jobs\\circulars", $view_data);
    }

    /* apply circular modal form */

    function apply_circular_modal_form($id = 1) {
        if (!$id) {
            app_redirect("recruitment_management");
        }

        validate_numeric_value($id);

        $view_data['topbar'] = "includes/public/topbar";
        $view_data['left_menu'] = false;

        $model_info = $this->Recruitment_jobs_model->get_one_where(array("id" => $id, "status" => "active", "deleted" => 0));

        if ($model_info->id) {
            $view_data['model_info'] = $model_info;
            return view("Recruitment_management\Views\jobs\\apply_circular_modal_form", $view_data);
        } else {
            show_404();
        }
    }

    /* save a candidate */

    function save_circular() {
        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required",
            "circular_id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $circular_id = $this->request->getPost('circular_id');

        $first_name = $this->request->getPost("first_name");
        $last_name = $this->request->getPost("last_name");
        $email = $this->request->getPost("email");

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "recruitment_resume");

        $candidate_data = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "gender" => $this->request->getPost("gender"),
            "dob" => $this->request->getPost('date_of_birth'),
            "phone" => $this->request->getPost('phone'),
            "address" => $this->request->getPost('address'),
            "education" => $this->request->getPost('education'),
            "work_experience" => $this->request->getPost('work_experience'),
            "circular_id" => $circular_id,
            "applied_at" => get_current_utc_time()
        );

        if (!$id) {
            $candidate_data["status_id"] = $this->request->getPost('status_id') ? $this->request->getPost('status_id') : 1;
        }

        $candidate_data["resume"] = $files_data;

        $candidate_id = $this->Recruitment_candidates_model->ci_save($candidate_data, $id);

        if ($candidate_id) {
            echo json_encode(array("success" => true, 'message' => app_lang('recruitment_circular_submitted') . " " . anchor(get_uri("recruitment_circulars"), app_lang('recruitment_more_circulars'), array("class" => "text-white text-off"))));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* recruitment circular public preview */

    function public_preview($circular_id = 0, $public_key = "") {
        if (!($circular_id && $public_key)) {
            show_404();
        }

        validate_numeric_value($circular_id);

        //check public key
        $circular_info = $this->Recruitment_jobs_model->get_one($circular_id);
        if ($circular_info->public_key !== $public_key) {
            show_404();
        }

        $view_data = array();

        $circular_data = get_recruitment_circular_making_data($circular_id);
        if (!$circular_data) {
            show_404();
        }

        $view_data['circular_preview'] = prepare_recruitment_circular_view($circular_data);
        $view_data['show_close_preview'] = false; //don't show back button
        $view_data['circular_id'] = $circular_id;
        $view_data['circular_type'] = "public";
        $view_data['public_key'] = clean_data($public_key);
        $view_data['circular_info'] = $circular_info;

        return view("Recruitment_management\Views\jobs\\circular_public_preview", $view_data);
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for ticket */

    function validate_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

}

/* End of file Recruitment_circulars.php */
/* Location: ./plugins/Recruitment_management/Controllers/Recruitment_circulars.php */