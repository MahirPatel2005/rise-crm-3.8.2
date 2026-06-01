<?php

namespace Recruitment_management\Controllers;

class Recruitment_circular_templates extends \App\Controllers\Security_Controller {

    protected $Recruitment_job_templates_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Recruitment_job_templates_model = new \Recruitment_management\Models\Recruitment_job_templates_model();
    }

    /* load the recruitment_circular_template view */

    function index() {
        return $this->template->view("Recruitment_management\Views\\recruitment_circular\index");
    }

    /* load the recruitment_circular_template add/edit modal */

    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Recruitment_job_templates_model->get_one($this->request->getPost('id'));
        $view_data['job_templates_dropdown'] = array("" => "-") + $this->Recruitment_job_templates_model->get_dropdown_list(array("title"), "id");
        return $this->template->view('Recruitment_management\Views\\recruitment_circular\modal_form', $view_data);
    }

    /* save a circular template */

    function save_template() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        $data = array(
            "template" => decode_ajax_post_data($this->request->getPost('template'))
        );
        $save_id = $this->Recruitment_job_templates_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* load template edit form */

    function form($id = "") {
        validate_numeric_value($id);
        $view_data['model_info'] = $this->Recruitment_job_templates_model->get_one($id);
        return $this->template->view('Recruitment_management\Views\\recruitment_circular\form', $view_data);
    }

    //save a recruitment_cuircular_template
    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $copy_template = $this->request->getPost('copy_template');
        $data = array(
            "title" => $this->request->getPost('title'),
        );

        if ($copy_template) {
            $recruitment_circular_template = $this->Recruitment_job_templates_model->get_one($copy_template);
            $data["template"] = $recruitment_circular_template->template;
        }

        $save_id = $this->Recruitment_job_templates_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo a recruitment_circular_template */

    function delete() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');
        if (get_setting("default_template") === $id) {
            app_redirect("forbidden");
        }

        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_job_templates_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_job_templates_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* get recruitment_circular_template list data */

    function list_data($view_type = "") {
        $list_data = $this->Recruitment_job_templates_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $view_type);
        }
        echo json_encode(array("data" => $result));
    }

    /* get a row of recruitment_circular_template list */

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_job_templates_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    /* make a row of recruitment_circular_template list table */

    private function _make_row($data, $view_type = "") {
        if ($view_type === "modal") {
            return array("<a href='#' data-id='$data->id' class='recruitment_circular_template-row link'>" . $data->title . "</a>");
        } else {
            $delete = "";

            if (get_setting("default_template") !== $data->id) {
                $delete = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_job_circular_template'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_circular_templates/delete"), "data-action" => "delete"));
            }

            return array("<a href='#' data-id='$data->id' class='recruitment_circular_template-row link'>" . $data->title . "</a>",
                "<a class='edit'><i data-feather='code' class='icon-16'></i></a>" . modal_anchor(get_uri("recruitment_circular_templates/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "", "title" => app_lang('recruitment_edit_job_circular_template'), "data-post-id" => $data->id))
                . $delete
            );
        }
    }

    /* show a modal to choose a template for recruitment circular */

    function insert_template_modal_form() {
        return $this->template->view("Recruitment_management\Views\\recruitment_circular\insert_template_modal_form");
    }

    function get_template_data($id = 0) {
        if (!$id) {
            show_404();
        }

        $template_info = $this->Recruitment_job_templates_model->get_one($id);
        echo json_encode(array("success" => true, 'template' => $template_info->template));
    }

}

/* End of file Recruitment_circular_templates.php */
/* Location: ./plugins/Recruitment_management/Controllers/Recruitment_circular_templates.php */