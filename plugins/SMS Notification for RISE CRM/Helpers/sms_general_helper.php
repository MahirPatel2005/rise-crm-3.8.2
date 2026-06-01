<?php

use App\Controllers\App_Controller;

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_sms_setting')) {

    function get_sms_setting($key = "") {
        $Sms_settings_model = new \SMS\Models\Sms_settings_model();
        return $Sms_settings_model->get_sms_setting($key);
    }

}

/*
 * Send SMS notification
 */
if (!function_exists('send_sms_notification')) {

    function send_sms_notification($data_array = array()) {

        $ci = new App_Controller();
        $Sms_notifications_model = new \SMS\Models\Sms_notifications_model();
        $Sms_templates_model = new \SMS\Models\Sms_templates_model();

        $notification = $Sms_notifications_model->get_sms_notification($data_array->id);
        $url = get_uri();
        $parser_data = array();
        $info = get_notification_config($notification->event, "info", $notification);

        if (is_array($info) && get_array_value($info, "url")) {
            $url = get_array_value($info, "url");
        }

        $parser_data["APP_TITLE"] = get_setting("app_title");
        $parser_data["COMPANY_NAME"] = get_setting("company_name");

        if ($notification->category == "ticket" && $notification->event !== "ticket_assigned") {
            $sms_template = $Sms_templates_model->get_final_sms_template($notification->event);

            $parser_data["TICKET_ID"] = $notification->ticket_id;
            $parser_data["TICKET_TITLE"] = $notification->ticket_title;
            $parser_data["USER_NAME"] = $notification->user_name;
            $parser_data["TICKET_CONTENT"] = nl2br($notification->ticket_comment_description);
            $parser_data["TICKET_URL"] = $url;
        } else if ($notification->event == "invoice_payment_confirmation") {
            $sms_template = $Sms_templates_model->get_final_sms_template("invoice_payment_confirmation");
            $parser_data["PAYMENT_AMOUNT"] = to_currency($notification->payment_amount, $notification->client_currency_symbol);
            $parser_data["INVOICE_ID"] = get_invoice_id($notification->payment_invoice_id);
            $parser_data["INVOICE_URL"] = $url;
        } else if ($notification->event == "new_message_sent" || $notification->event == "message_reply_sent") {
            $sms_template = $Sms_templates_model->get_final_sms_template("message_received");

            $message_info = $ci->Messages_model->get_details(array("id" => $notification->actual_message_id))->row;

            //reply? find the subject from the parent meessage
            if ($notification->event == "message_reply_sent") {
                $main_message_info = $ci->Messages_model->get_details(array("id" => $message_info->message_id))->row;
                $parser_data["SUBJECT"] = $main_message_info->subject;
            }

            $parser_data["SUBJECT"] = $message_info->subject;
            $parser_data["USER_NAME"] = $message_info->user_name;
            $parser_data["MESSAGE_CONTENT"] = nl2br($message_info->message);
            $parser_data["MESSAGE_URL"] = $url;
        } else if ($notification->event == "recurring_invoice_created_vai_cron_job" || $notification->event == "invoice_due_reminder_before_due_date" || $notification->event == "invoice_overdue_reminder" || $notification->event == "recurring_invoice_creation_reminder") {

            //get the specific email template
            if ($notification->event == "recurring_invoice_created_vai_cron_job") {
                $sms_template = $Sms_templates_model->get_final_sms_template("send_invoice");
            } else if ($notification->event == "invoice_due_reminder_before_due_date") {
                $sms_template = $Sms_templates_model->get_final_sms_template("invoice_due_reminder_before_due_date");
            } else if ($notification->event == "invoice_overdue_reminder") {
                $sms_template = $Sms_templates_model->get_final_sms_template("invoice_overdue_reminder");
            } else if ($notification->event == "recurring_invoice_creation_reminder") {
                $sms_template = $Sms_templates_model->get_final_sms_template("recurring_invoice_creation_reminder");
            }

            $invoice_data = get_invoice_making_data($notification->invoice_id);
            $invoice_info = get_array_value($invoice_data, "invoice_info");
            $invoice_total_summary = get_array_value($invoice_data, "invoice_total_summary");

            $primary_contact = $ci->Clients_model->get_primary_contact($invoice_info->client_id, true);

            $parser_data["INVOICE_ID"] = $notification->invoice_id;
            $parser_data["CONTACT_FIRST_NAME"] = $primary_contact->first_name;
            $parser_data["CONTACT_LAST_NAME"] = $primary_contact->last_name;
            $parser_data["BALANCE_DUE"] = to_currency($invoice_total_summary->balance_due, $invoice_total_summary->currency_symbol);
            $parser_data["DUE_DATE"] = format_to_date($invoice_info->due_date, false);
            $parser_data["PROJECT_TITLE"] = $invoice_info->project_title;
            $parser_data["INVOICE_URL"] = $url;

            if ($notification->event == "recurring_invoice_creation_reminder") {
                $parser_data["NEXT_RECURRING_DATE"] = format_to_date($invoice_info->next_recurring_date, false);
            }
        } else if ($notification->category == "estimate") {
            if ($notification->event == "estimate_request_received") {
                $sms_template = $Sms_templates_model->get_final_sms_template("estimate_request_received");

                $estimate_request_info = $ci->Estimate_requests_model->get_one($notification->estimate_request_id);
                $primary_contact = $ci->Clients_model->get_primary_contact($estimate_request_info->client_id, true);

                $parser_data["CONTACT_FIRST_NAME"] = $primary_contact->first_name;
                $parser_data["CONTACT_LAST_NAME"] = $primary_contact->last_name;

                $parser_data["ESTIMATE_REQUEST_ID"] = $notification->estimate_request_id;
                $parser_data["ESTIMATE_REQUEST_URL"] = $url;
            } else {
                if ($notification->event == "estimate_rejected") {
                    $sms_template = $Sms_templates_model->get_final_sms_template("estimate_rejected");
                } else if ($notification->event == "estimate_accepted") {
                    $sms_template = $Sms_templates_model->get_final_sms_template("estimate_accepted");
                }

                $parser_data["ESTIMATE_ID"] = $notification->estimate_id;
                $parser_data["ESTIMATE_URL"] = $url;
            }
        } else if ($notification->category == "order") {
            if ($notification->event == "new_order_received") {
                $sms_template = $Sms_templates_model->get_final_sms_template("new_order_received");
            } else {
                $sms_template = $Sms_templates_model->get_final_sms_template("order_status_updated");
            }

            $order_info = $ci->Orders_model->get_one($notification->order_id);
            $primary_contact = $ci->Clients_model->get_primary_contact($order_info->client_id, true);

            $parser_data["CONTACT_FIRST_NAME"] = $primary_contact->first_name;
            $parser_data["CONTACT_LAST_NAME"] = $primary_contact->last_name;

            $parser_data["ORDER_ID"] = $notification->order_id;
            $parser_data["ORDER_URL"] = $url;
        } else {
            $sms_template = $Sms_templates_model->get_final_sms_template("general_notification");

            $parser_data["EVENT_TITLE"] = $notification->user_name . " " . sprintf(app_lang("notification_" . $notification->event), $notification->to_user_name);
            $parser_data["NOTIFICATION_URL"] = $url;

            $view_data["notification"] = $notification;
            $parser_data["EVENT_DETAILS"] = view("SMS\Views\sms/notifications/sms_notification_description", $view_data);
        }

        $parser_data["SIGNATURE"] = $sms_template->signature;

        $parser = \Config\Services::parser();
        $message = $parser->setData($parser_data)->renderString($sms_template->message);

        $notify_to = $data_array->notify_to;
        $sms_notify_to_array = explode(",", $notify_to);

        foreach ($sms_notify_to_array as $user_id) {
            $user_info = $ci->Users_model->get_one($user_id);

            //send sms to users
            send_sms($message, $user_info->phone);

            //also save sms notification logs
            $Sms_notification_logs_model = new \SMS\Models\Sms_notification_logs_model();
            $data = array(
                "message" => $message,
                "created_at" => get_current_utc_time(),
                "notify_to" => $user_id,
                "event" => $notification->event,
                "category" => $notification->category
            );

            $Sms_notification_logs_model->ci_save($data);
        }
    }

}

/**
 * send SMS
 * 
 * @param string $message
 * @param array $phone
 */
if (!function_exists('send_sms')) {

    function send_sms($message, $phone) {

        require_once(PLUGINPATH . "SMS/ThirdParty/Twilio/vendor/autoload.php");

        // Account SID and Auth Token
        $twilio_account_sid = get_sms_setting("twilio_account_sid");
        $twilio_auth_token = get_sms_setting("twilio_auth_token");
        $twilio_phone_number = get_sms_setting("twilio_phone_number");

        $client = new \Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);

        //Use the client to send text messages!
        $client->messages->create(
                // the number you'd like to send the message to
                $phone,
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => $twilio_phone_number,
                    // the body of the text message you'd like to send
                    'body' => $message
                ]
        );
    }

}