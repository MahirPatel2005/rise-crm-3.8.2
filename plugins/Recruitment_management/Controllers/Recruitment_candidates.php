<?php

namespace Recruitment_management\Controllers;

class Recruitment_candidates extends \App\Controllers\Security_Controller {

    protected $Recruitment_jobs_model;
    protected $Recruitment_candidates_model;
    protected $Recruitment_candidate_events_model;
    protected $Recruitment_event_types_model;
    protected $Recruitment_candidate_notes_model;

    function __construct() {
        parent::__construct();
        $this->Recruitment_jobs_model = new \Recruitment_management\Models\Recruitment_jobs_model();
        $this->Recruitment_hiring_stages_model = new \Recruitment_management\Models\Recruitment_hiring_stages_model();
        $this->Recruitment_candidates_model = new \Recruitment_management\Models\Recruitment_candidates_model();
        $this->Recruitment_candidate_events_model = new \Recruitment_management\Models\Recruitment_candidate_events_model();
        $this->Recruitment_event_types_model = new \Recruitment_management\Models\Recruitment_event_types_model();
        $this->Recruitment_candidate_notes_model = new \Recruitment_management\Models\Recruitment_candidate_notes_model();
    }

    //check access premission for candidates
    private function can_access_candidates($candidate_id = "") {
        if ($this->login_user->user_type == "staff") {
            if ($this->login_user->is_admin) {
                return true;
            } else if ($candidate_id) {
                if ($candidate_id) {
                    //user has permission to view only assigned circulars
                    $candidates_info = $this->Recruitment_candidates_model->get_one($candidate_id);
                    $circular_info = $this->Recruitment_jobs_model->get_one($candidates_info->circular_id);
                    $recruiters_array = explode(',', $circular_info->recruiters);
                    if (in_array($this->login_user->id, $recruiters_array)) {
                        return true;
                    }
                }
            }
        }
    }

    /* load candidates view */

    function index() {
        $view_data['hiring_stage_dropdown'] = json_encode($this->_get_hiring_stage_dropdown_select2_data());
        $view_data['circular_dropdown'] = json_encode($this->_get_circular_dropdown_select2_data());

        return $this->template->rander("Recruitment_management\Views\candidates\index", $view_data);
    }

    /* list of candidates, prepared for datatable  */

    function list_data($circular_id = 0) {
        //only admin can see all candidates, other team mebers can see only their the candidates where they are recruiters.
        if ($circular_id) {
            $options = array("circular_id" => $circular_id);
        } else {
            $options = array(
                "hiring_stage" => $this->request->getPost('hiring_stage'),
                "circular_id" => $this->request->getPost('circular_id')
            );
        }
        if (!$this->can_access_candidates()) {
            $options["user_id"] = $this->login_user->id;
        }

        $list_data = $this->Recruitment_candidates_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of candidate list  table */

    function _row_data($id) {
        $data = $this->Recruitment_candidates_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($data);
    }

    /* prepare a row of candidate list table */

    function _make_row($data) {
        $name_short = $data->first_name[0] . $data->last_name[0];

        $title = "<div class='custom-avatar float-start me-3 d-flex' style='background-color: #2E4053'><span class='text-white'>$name_short</span></div>" . "<span class='strong'>" . anchor(get_uri("recruitment_candidates/view/" . $data->id), $data->name) . "</span>" . "<div class='text-off'>$data->email</div>";

        if ($data->hiring_stage == "New") {
            $status_class = "bg-orange";
        } else if ($data->hiring_stage == "Hired") {
            $status_class = "bg-success";
        } else if ($data->hiring_stage == "Disqualified") {
            $status_class = "bg-danger";
        } else {
            $status_class = "bg-secondary";
        }

        $hiring_stage = "<span class='badge $status_class large'>" . $data->hiring_stage . "</span> ";

        $actions = modal_anchor(get_uri("recruitment_candidates/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('recruitment_edit_candidate'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('recruitment_delete_candidate'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_candidates/delete"), "data-action" => "delete-confirmation"));

        return array(
            $title,
            $data->circular_title,
            $hiring_stage,
            $actions
        );
    }

    protected function _get_hiring_stage_dropdown_select2_data() {
        $hiring_stages = $this->Recruitment_hiring_stages_model->get_all()->getResult();

        $hiring_stage_dropdown[] = array("id" => "", "text" => "- " . app_lang("recruitment_hiring_stages") . " -");

        foreach ($hiring_stages as $hiring_stage) {
            $hiring_stage_dropdown[] = array("id" => $hiring_stage->id, "text" => $hiring_stage->title);
        }
        return $hiring_stage_dropdown;
    }

    protected function _get_circular_dropdown_select2_data() {
        $circulars = $this->Recruitment_jobs_model->get_all()->getResult();

        $circular_dropdown[] = array("id" => "", "text" => "- " . app_lang("recruitment_circulars") . " -");

        foreach ($circulars as $circular) {
            $circular_dropdown[] = array("id" => $circular->id, "text" => $circular->job_title);
        }
        return $circular_dropdown;
    }

    /* candidate add/edit modal form */

    function modal_form() {
        $model_info = $this->Recruitment_candidates_model->get_one($this->request->getPost('id'));
        $view_data['model_info'] = $model_info;

        return $this->template->view('Recruitment_management\Views\candidates\modal_form', $view_data);
    }

    /* delete a candidate */

    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->Recruitment_candidates_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /* candidate view */

    function view($id = 0, $tab = "") {
        if ($id) {
            if (!$this->can_access_candidates($id)) {
                app_redirect("forbidden");
            }
        }

        $view_data['candidate_info'] = $this->Recruitment_candidates_model->get_details(array("id" => $id))->getRow();
        $view_data['tab'] = clean_data($tab);

        $view_data['stages'] = $this->Recruitment_hiring_stages_model->get_details()->getResult();

        return $this->template->rander('Recruitment_management\Views\candidates\view', $view_data);
    }

    /* upadate a candidate status */

    function save_candidate_status($id = 0) {
        validate_numeric_value($id);
        if ($id) {
            if (!$this->can_access_candidates($id)) {
                app_redirect("forbidden");
            }
        }

        $status_id = $this->request->getPost('value');
        $data = array(
            "status_id" => $status_id
        );

        $save_id = $this->Recruitment_candidates_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /* canidate details tab */

    function applicant_details_tab($applicant_id = 0) {
        if ($applicant_id) {
            $view_data['model_info'] = $this->Recruitment_candidates_model->get_details(array("id" => $applicant_id))->getRow();

            return $this->template->view('Recruitment_management\Views\candidates\applicant_details_tab', $view_data);
        }
    }

    /* candidate attachments tab */

    function attachments($applicant_id = 0) {
        if ($applicant_id) {
            $view_data['model_info'] = $this->Recruitment_candidates_model->get_details(array("id" => $applicant_id))->getRow();

            return $this->template->view('Recruitment_management\Views\candidates\attachments_tab', $view_data);
        }
    }

    function file_preview($id = "", $key = "", $tab = "") {
        if ($id) {
            validate_numeric_value($id);

            if ($tab == "notes_tab") {
                $candidate_note_info = $this->Recruitment_candidate_notes_model->get_one($id);
                $files = unserialize($candidate_note_info->files);
            } else {
                $candidate_info = $this->Recruitment_candidates_model->get_one($id);
                $files = unserialize($candidate_info->resume);
            }

            $file = get_array_value($files, $key);

            $file_name = get_array_value($file, "file_name");
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");

            $view_data["file_url"] = get_source_url_of_file($file, get_setting("timeline_file_path"));
            $view_data["is_image_file"] = is_image_file($file_name);
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_name);
            $view_data["is_google_drive_file"] = ($file_id && $service_type == "google") ? true : false;

            return $this->template->view('Recruitment_management\Views\candidates\file_preview', $view_data);
        } else {
            show_404();
        }
    }

    /* download files */

    function download_files($id) {
        validate_numeric_value($id);

        $files = $this->Recruitment_candidates_model->get_one($id)->resume;
        return $this->download_app_files(get_setting("timeline_file_path"), $files);
    }

    /* candidate events tab */

    function events($applicant_id = 0) {
        if ($applicant_id) {
            $view_data['model_info'] = $this->Recruitment_candidates_model->get_details(array("id" => $applicant_id))->getRow();

            return $this->template->view('Recruitment_management\Views\candidates\events', $view_data);
        }
    }

    /* add/edit candidate event modal */

    function add_event_modal_form() {
        $id = $this->request->getPost('id');

        $model_info = $this->Recruitment_candidate_events_model->get_one($id);
        $view_data['model_info'] = $model_info;
        $view_data['candidate_id'] = $this->request->getPost('candidate_id') ? $this->request->getPost('candidate_id') : $model_info->candidate_id;

        $view_data['event_types_dropdown'] = $this->_get_event_types_dropdown_list();
        $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;

        return $this->template->view('Recruitment_management\Views\candidates\add_event_modal_form', $view_data);
    }

    private function _get_event_types_dropdown_list() {
        $event_types = $this->Recruitment_event_types_model->get_details(array("deleted" => 0))->getResult();
        $event_type_dropdown = array(array("id" => "", "text" => "- " . app_lang("recruitment_event_type") . " -"));

        foreach ($event_types as $event_type) {
            $event_type_dropdown[] = array("id" => $event_type->id, "text" => $event_type->title);
        }
        return $event_type_dropdown;
    }

    /* save a candidate event */

    function save_event() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "candidate_id" => "required",
            "event_type_id" => "required"
        ));

        $id = $this->request->getPost('id');
        $candidate_id = $this->request->getPost('candidate_id');

        //convert to 24hrs time format
        $start_time = $this->request->getPost('start_time');
        $end_time = $this->request->getPost('end_time');

        if (get_setting("time_format") != "24_hours") {
            $start_time = convert_time_to_24hours_format($start_time);
            $end_time = convert_time_to_24hours_format($end_time);
        }

        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');

        $data = array(
            "candidate_id" => $candidate_id,
            "event_type_id" => $this->request->getPost('event_type_id'),
            "start_date" => $start_date,
            "end_date" => $end_date,
            "start_time" => $start_time,
            "end_time" => $end_time,
            "location" => $this->request->getPost('location'),
            "description" => $this->request->getPost('description')
        );

        if (!$id) {
            $data["created_at"] = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;
        }

        $save_id = $this->Recruitment_candidate_events_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_event_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of candidate events, prepared for datatable  */

    function event_list_data($candidate_id = 0) {
        validate_numeric_value($candidate_id);

        $options = array("candidate_id" => $candidate_id);
        $list_data = $this->Recruitment_candidate_events_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_candidate_event_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of candidate event list  table */

    private function _event_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_candidate_events_model->get_details($options)->getRow();

        return $this->_make_candidate_event_row($data);
    }

    /* prepare a row of candidate event list table */

    private function _make_candidate_event_row($data) {
        //define event color based on start date
        $start_time = date("L", strtotime($data->start_time));
        $label_class = "";
        if ($start_time == get_my_local_time("Y-m-d")) {
            $label_class = "bg-warning";
        } else if (get_my_local_time("Y-m-d") > $data->start_time) {
            $label_class = "bg-danger";
        } else {
            $label_class = "bg-primary";
        }

        $day_or_year_name = "";
        if (date("Y", strtotime(get_current_utc_time())) === date("Y", strtotime($data->start_time))) {
            $day_or_year_name = app_lang(strtolower(date("l", strtotime($data->start_time)))); //get day name from language
        } else {
            $day_or_year_name = date("Y", strtotime($data->start_time)); //get current year
        }

        $month_name = app_lang(strtolower(date("F", strtotime($data->start_time)))); //get month name from language

        $start_time = "<div class='milestone float-start rounded' title='" . format_to_date($data->start_time) . "'>
            <span class='badge $label_class m0 rounded-top'>" . $month_name . "</span>
            <h4 class='mb0'>" . date("d", strtotime($data->start_time)) . "</h4>
            <small>" . $day_or_year_name . "</small>
            </div>
            ";

        $event_details = "<div>
            <div class='strong'>" . $data->event_title . "</div>
            <div class='font-14 text-off'><span data-feather='user' class='icon-14'></span> " . $data->candidate_name . "</div>
            <div class='font-14 text-off'><span data-feather='clock' class='icon-14'></span> " . $data->start_date . ", " . $data->start_time . " - " . $data->end_date . ", " . $data->end_time . "</div>
            </div>";

        $optoins = modal_anchor(get_uri("recruitment_candidates/add_event_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_milestone'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_milestone'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_candidates/delete_event"), "data-action" => "delete"));

        return array(
            $data->start_time,
            $start_time,
            $event_details,
            $optoins
        );
    }

    /* delete or undo a candidate event */

    function delete_event() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');
        if (get_setting("default_template") === $id) {
            app_redirect("forbidden");
        }

        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_candidate_events_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_event_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_candidate_events_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* candidate notes tab */

    function notes($applicant_id = 0) {
        if ($applicant_id) {
            $view_data['model_info'] = $this->Recruitment_candidates_model->get_details(array("id" => $applicant_id))->getRow();

            return $this->template->view('Recruitment_management\Views\candidates\notes', $view_data);
        }
    }

    /* add/edit candidate note modal */

    function add_note_modal_form() {
        $id = $this->request->getPost('id');

        $model_info = $this->Recruitment_candidate_notes_model->get_one($id);
        $view_data['model_info'] = $model_info;
        $view_data['candidate_id'] = $this->request->getPost('candidate_id') ? $this->request->getPost('candidate_id') : $model_info->candidate_id;

        return $this->template->view('Recruitment_management\Views\candidates\add_note_modal_form', $view_data);
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for notes */

    function validate_notes_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* save a candidate note */

    function save_note() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "candidate_id" => "required"
        ));

        $id = $this->request->getPost('id');
        $candidate_id = $this->request->getPost('candidate_id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "recruitment_candidate_note");
        $new_files = unserialize($files_data);

        $data = array(
            "candidate_id" => $candidate_id,
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description')
        );

        if (!$id) {
            $data["created_at"] = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;
        }

        if ($id) {
            $note_info = $this->Notes_model->get_one($id);
            $timeline_file_path = get_setting("timeline_file_path");

            $new_files = update_saved_files($timeline_file_path, $note_info->files, $new_files);
        }

        $data["files"] = serialize($new_files);

        $save_id = $this->Recruitment_candidate_notes_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_notes_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo a candidate note */

    function delete_note() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');
        if (get_setting("default_template") === $id) {
            app_redirect("forbidden");
        }

        if ($this->request->getPost('undo')) {
            if ($this->Recruitment_candidate_notes_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_notes_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Recruitment_candidate_notes_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of candidate notes, prepared for datatable  */

    function notes_list_data($candidate_id = 0) {
        validate_numeric_value($candidate_id);

        $options = array("candidate_id" => $candidate_id);
        $list_data = $this->Recruitment_candidate_notes_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_candidate_notes_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of candidate note list  table */

    private function _notes_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Recruitment_candidate_notes_model->get_details($options)->getRow();

        return $this->_make_candidate_notes_row($data);
    }

    /* prepare a row of candidate note list table */

    private function _make_candidate_notes_row($data) {
        $title = modal_anchor(get_uri("recruitment_candidates/note_view/" . $data->id), $data->title, array("title" => app_lang('note'), "data-post-id" => $data->id));

        $files_link = "";
        if ($data->files) {
            $files = unserialize($data->files);
            if (count($files)) {
                foreach ($files as $key => $value) {
                    $file_name = get_array_value($value, "file_name");
                    $link = get_file_icon(strtolower(pathinfo($file_name, PATHINFO_EXTENSION)));
                    $files_link .= js_anchor("<i data-feather='$link'></i>", array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "class" => "float-start font-22 mr10", "title" => remove_file_prefix($file_name), "data-url" => get_uri("recruitment_candidates/file_preview/" . $data->id . "/" . $key . "/notes_tab")));
                }
            }
        }

        $optoins = modal_anchor(get_uri("recruitment_candidates/add_note_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_milestone'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_milestone'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("recruitment_candidates/delete_note"), "data-action" => "delete"));

        return array(
            $data->created_at,
            $title,
            $files_link,
            $optoins
        );
    }

    /* candidate note view */

    function note_view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Recruitment_candidate_notes_model->get_details(array("id" => $this->request->getPost('id')))->getRow();

        $view_data['model_info'] = $model_info;
        return $this->template->view('Recruitment_management\Views\candidates\note_view', $view_data);
    }

    /* send email to candidate */

    function send_email_modal_form($candidate_id = 0) {
        if ($candidate_id) {
            $candidate_info = $this->Recruitment_candidates_model->get_one($candidate_id);
            $view_data['candidate_info'] = $candidate_info;

            return $this->template->view('Recruitment_management\Views\candidates\send_email_modal_form', $view_data);
        } else {
            show_404();
        }
    }

    /* trigger email */

    function send_email() {
        $this->validate_submitted_data(array(
            "candidate_id" => "required|numeric"
        ));

        $candidate_id = $this->request->getPost('candidate_id');

        $subject = $this->request->getPost('subject');
        $message = decode_ajax_post_data($this->request->getPost('message'));

        $contact = $this->Recruitment_candidates_model->get_one($candidate_id);

        if (send_app_mail($contact->email, $subject, $message, array())) {
            $data = array("last_email_sent_date" => get_my_local_time());
            if ($this->Recruitment_candidates_model->ci_save($data, $candidate_id)) {
                echo json_encode(array('success' => true, 'message' => app_lang("recruitment_email_sent_message")));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

}

/* End of file Recruitment_candidates.php */
/* Location: ./plugins/Recruitment_management/Controllers/Recruitment_candidates.php */