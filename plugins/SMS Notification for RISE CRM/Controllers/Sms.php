<?php

namespace SMS\Controllers;

class Sms extends \App\Controllers\Security_Controller {

    protected $Sms_settings_model;
    protected $Sms_notification_settings_model;

    function __construct() {
        parent::__construct();
        $this->Sms_settings_model = new \SMS\Models\Sms_settings_model();
        $this->Sms_notification_settings_model = new \SMS\Models\Sms_notification_settings_model();
        $this->Sms_templates_model = new \SMS\Models\Sms_templates_model();
    }

    //load twilio setup settings view
    function index() {
        return $this->template->rander("SMS\Views\sms\index");
    }

    //save twilio sms settings
    function save_twilio_sms_settings() {
        $settings = array("enable_sms", "twilio_account_sid", "twilio_auth_token", "twilio_phone_number");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Sms_settings_model->save_sms_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    //load test sms notification modal
    function send_test_sms_modal_form() {
        return $this->template->view('SMS\Views\sms\send_test_sms_modal_form');
    }

    //send a test sms notification
    function send_test_sms() {
        $this->validate_submitted_data(array(
            "phone" => "required",
            "message" => "required"
        ));

        $phone = $this->request->getPost('phone');
        $message = $this->request->getPost('message');

        require_once(PLUGINPATH . "SMS/ThirdParty/Twilio/vendor/autoload.php");

        // Account SID, Auth Token and your twilio phone number
        $twilio_account_sid = get_sms_setting("twilio_account_sid");
        $twilio_auth_token = get_sms_setting("twilio_auth_token");
        $twilio_phone_number = get_sms_setting("twilio_phone_number");

        $client = new \Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);

        //Use the client to send text messages
        $success = $client->messages->create(
                //the number you'd like to send the message to
                $phone,
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => $twilio_phone_number,
                    // the body of the text message you'd like to send
                    'body' => $message
                ]
        );

        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('sms_send_test_sms_successfull_message')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('sms_send_test_sms_error_message')));
        }
    }

    //load sms notification settings tab
    function sms_notification_settings() {
        $category_suggestions = array(
            array("id" => "", "text" => "- " . app_lang('category') . " -"),
            array("id" => "announcement", "text" => app_lang("announcement")),
            array("id" => "client", "text" => app_lang("client")),
            array("id" => "event", "text" => app_lang("event")),
            array("id" => "estimate", "text" => app_lang("estimate")),
            array("id" => "invoice", "text" => app_lang("invoice")),
            array("id" => "leave", "text" => app_lang("leave")),
            array("id" => "lead", "text" => app_lang("lead")),
            array("id" => "message", "text" => app_lang("message")),
            array("id" => "order", "text" => app_lang("order")),
            array("id" => "project", "text" => app_lang("project")),
            array("id" => "ticket", "text" => app_lang("ticket")),
            array("id" => "timeline", "text" => app_lang("timeline"))
        );

        $view_data['categories_dropdown'] = json_encode($category_suggestions);
        return $this->template->view("SMS\Views\sms\\notifications\index", $view_data);
    }

    /* list of sms notification, prepared for datatable  */

    function notification_settings_list_data() {
        $options = array("category" => $this->request->getPost("category"));
        $list_data = $this->Sms_notification_settings_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_sms_notification_settings_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of sms notification  */

    private function _sms_notification_list_data($id) {
        $options = array("id" => $id);
        $data = $this->Sms_notification_settings_model->get_details($options)->getRow();
        return $this->_make_sms_notification_settings_row($data);
    }

    /* prepare a row of sms notification list table */

    private function _make_sms_notification_settings_row($data) {

        $yes = "<i data-feather='check-circle' class='icon-16'></i>";
        $no = "<i data-feather='check-circle' class='icon-16' style='opacity:0.2'></i>";

        return array(
            $data->sort,
            app_lang($data->event),
            app_lang($data->category),
            $data->enable_sms ? $yes : $no,
            modal_anchor(get_uri("sms/notification_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('notification'), "data-post-id" => $data->id))
        );
    }

    /* load sms notification modal */

    function notification_modal_form() {
        $id = $this->request->getPost("id");

        if ($id) {
            $view_data["model_info"] = $this->Sms_notification_settings_model->get_details(array("id" => $id))->getRow();
        }
        return $this->template->view('SMS\Views\sms\notifications\modal_form', $view_data);
    }

    /* save sms notification */

    function save_sms_notification_settings() {
        $id = $this->request->getPost("id");

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $data = array(
            "enable_sms" => $this->request->getPost("enable_sms")
        );

        $save_id = $this->Sms_notification_settings_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_sms_notification_list_data($save_id), 'id' => $save_id, 'message' => app_lang('settings_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* load sms template tab */

    function sms_notification_template() {
        return $this->template->view("SMS\Views\sms\\template\index");
    }

    /* list of sms template, prepared for datatable  */

    function sms_template_list_data() {
        $list_data = $this->Sms_templates_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_sms_template_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _sms_template_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Sms_templates_model->get_details($options)->getRow();
        return $this->_make_sms_template_row($data);
    }

    /* prepare a row of sms template list table */

    function _make_sms_template_row($data) {
        $template_name = app_lang($data->template_name);
        return array(
            $template_name,
            modal_anchor(get_uri("sms/edit_sms_template_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('sms_edit_sms_template'), "data-post-id" => $data->id, "data-post-template_name" => $data->template_name))
        );
    }

    //list of sms templates
    private function _sms_templates() {
        $templates_array = array(
            "account" => array(
                "login_info" => array("USER_FIRST_NAME", "USER_LAST_NAME", "DASHBOARD_URL", "USER_LOGIN_EMAIL", "USER_LOGIN_PASSWORD", "SIGNATURE"),
                "reset_password" => array("ACCOUNT_HOLDER_NAME", "RESET_PASSWORD_URL", "SITE_URL", "SIGNATURE"),
                "team_member_invitation" => array("INVITATION_SENT_BY", "INVITATION_URL", "SITE_URL", "SIGNATURE"),
                "new_client_greetings" => array("CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "COMPANY_NAME", "DASHBOARD_URL", "CONTACT_LOGIN_EMAIL", "CONTACT_LOGIN_PASSWORD", "SIGNATURE"),
                "client_contact_invitation" => array("INVITATION_SENT_BY", "INVITATION_URL", "SITE_URL", "SIGNATURE"),
                "verify_email" => array("VERIFY_EMAIL_URL", "SITE_URL", "SIGNATURE"),
            ),
            "project" => array(
                "project_task_deadline_reminder" => array("APP_TITLE", "DEADLINE", "SIGNATURE", "TASKS_LIST", "SIGNATURE"),
            ),
            "invoice" => array(
                "send_invoice" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "SIGNATURE", "INVOICE_URL", "PUBLIC_PAY_INVOICE_URL", "SIGNATURE"),
                "invoice_payment_confirmation" => array("INVOICE_ID", "PAYMENT_AMOUNT", "INVOICE_URL", "SIGNATURE"),
                "invoice_due_reminder_before_due_date" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "INVOICE_URL", "SIGNATURE"),
                "invoice_overdue_reminder" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "INVOICE_URL", "SIGNATURE"),
                "recurring_invoice_creation_reminder" => array("CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "APP_TITLE", "INVOICE_URL", "NEXT_RECURRING_DATE", "SIGNATURE"),
            ),
            "estimate" => array(
                "estimate_sent" => array("ESTIMATE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "ESTIMATE_URL", "SIGNATURE"),
                "estimate_accepted" => array("ESTIMATE_ID", "ESTIMATE_URL", "SIGNATURE"),
                "estimate_rejected" => array("ESTIMATE_ID", "ESTIMATE_URL", "SIGNATURE"),
                "estimate_request_received" => array("ESTIMATE_REQUEST_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "ESTIMATE_REQUEST_URL", "SIGNATURE"),
            ),
            "order" => array(
                "new_order_received" => array("ORDER_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "ORDER_URL", "SIGNATURE"),
                "order_status_updated" => array("ORDER_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "ORDER_URL", "SIGNATURE"),
            ),
            "ticket" => array(
                "ticket_created" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_CONTENT", "TICKET_URL", "SIGNATURE"),
                "ticket_commented" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_CONTENT", "TICKET_URL", "SIGNATURE"),
                "ticket_closed" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_URL", "SIGNATURE"),
                "ticket_reopened" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_URL", "SIGNATURE"),
            ),
            "message" => array(
                "message_received" => array("SUBJECT", "USER_NAME", "MESSAGE_CONTENT", "MESSAGE_URL", "APP_TITLE", "SIGNATURE"),
            ),
            "common" => array(
                "general_notification" => array("EVENT_TITLE", "EVENT_DETAILS", "APP_TITLE", "COMPANY_NAME", "NOTIFICATION_URL", "SIGNATURE")
            )
        );
        return $templates_array;
    }

    //load sms template modal
    function edit_sms_template_modal_form() {
        $template_name = $this->request->getPost("template_name");
        $view_data['model_info'] = $this->Sms_templates_model->get_one_where(array("template_name" => $template_name));
        $variables_array = array_column($this->_sms_templates(), $template_name);
        $variables = get_array_value($variables_array, 0);
        $view_data['variables'] = $variables ? $variables : array();

        return $this->template->view("SMS\Views\sms\\template\modal_form", $view_data);
    }

    /* save sms template */

    function save_sms_template() {
        $id = $this->request->getPost("id");

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "message" => "required"
        ));

        $data = array(
            "custom_message" => $this->request->getPost("message")
        );

        $save_id = $this->Sms_templates_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_sms_template_row_data($save_id), 'id' => $save_id, 'message' => app_lang('settings_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //restore sms template to default
    function restore_to_default() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $template_id = $this->request->getPost('id');

        $data = array(
            "custom_message" => ""
        );
        $save_id = $this->Sms_templates_model->ci_save($data, $template_id);
        if ($save_id) {
            $default_message = $this->Sms_templates_model->get_one($save_id)->default_message;
            echo json_encode(array("success" => true, "data" => $default_message, 'message' => app_lang('template_restored')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

}

/* End of file Sms.php */
/* Location: ./plugins/SMS/controllers/Sms.php */