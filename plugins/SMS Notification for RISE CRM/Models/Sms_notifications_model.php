<?php

namespace SMS\Models;

class Sms_notifications_model extends \App\Models\Crud_model {

    protected $table = null;
    private $Notifications_model;

    function __construct() {
        $this->table = 'sms_notification_settings';
        $this->Notifications_model = model("App\Models\Notifications_model");
        parent::__construct($this->table);
    }

    function create_sms_notification($notification_id) {
        $notification_info = $this->Notifications_model->get_one_where(array("id" => $notification_id));

        $sms_notification_settings_table = $this->db->prefixTable('sms_notification_settings');

        $sms_notification_settings = $this->db->query("SELECT * FROM $sms_notification_settings_table WHERE  $sms_notification_settings_table.event='$notification_info->event' AND $sms_notification_settings_table.enable_sms")->getRow();
        if (!$sms_notification_settings) {
            return false; //no notification settings found
        }
        send_sms_notification($notification_info);
    }

    function get_sms_notification($notification_id) {
        $notifications_table = $this->db->prefixTable('notifications');
        $users_table = $this->db->prefixTable('users');
        $projects_table = $this->db->prefixTable('projects');
        $project_comments_table = $this->db->prefixTable('project_comments');
        $project_files_table = $this->db->prefixTable('project_files');
        $tasks_table = $this->db->prefixTable('tasks');
        $leave_applications_table = $this->db->prefixTable('leave_applications');
        $tickets_table = $this->db->prefixTable('tickets');
        $ticket_comments_table = $this->db->prefixTable('ticket_comments');
        $activity_logs_table = $this->db->prefixTable('activity_logs');
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $posts_table = $this->db->prefixTable('posts');
        $invoices_table = $this->db->prefixTable('invoices');
        $clients_table = $this->db->prefixTable('clients');
        $events_table = $this->db->prefixTable('events');
        $notification_settings_table = $this->db->prefixTable('notification_settings');
        $announcement_table = $this->db->prefixTable('announcements');
        $orders_table = $this->db->prefixTable('orders');

        $sql = "SELECT $notifications_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS user_name,
                 $projects_table.title AS project_title,
                 $project_comments_table.description AS project_comment_title,
                 $project_files_table.file_name AS project_file_title,
                 $tasks_table.title AS task_title,
                 $events_table.title AS event_title,        
                 $tickets_table.title AS ticket_title,
                 $ticket_comments_table.description AS ticket_comment_description,
                 $posts_table.description AS posts_title,
                 $announcement_table.title AS announcement_title,
                 $activity_logs_table.changes AS activity_log_changes, $activity_logs_table.log_type AS activity_log_type,
                 $leave_applications_table.start_date AS leave_start_date, $leave_applications_table.end_date AS leave_end_date,
                 $invoice_payments_table.invoice_id AS payment_invoice_id, $invoice_payments_table.amount AS payment_amount, (SELECT currency_symbol FROM $clients_table WHERE $clients_table.id=$invoices_table.client_id) AS client_currency_symbol,
                 (SELECT CONCAT($users_table.first_name, ' ', $users_table.last_name) FROM $users_table WHERE $users_table.id=$notifications_table.to_user_id) AS to_user_name,
                 $notification_settings_table.category 
        FROM $notifications_table
        LEFT JOIN $projects_table ON $projects_table.id=$notifications_table.project_id
        LEFT JOIN $project_comments_table ON $project_comments_table.id=$notifications_table.project_comment_id
        LEFT JOIN $project_files_table ON $project_files_table.id=$notifications_table.project_file_id
        LEFT JOIN $tasks_table ON $tasks_table.id=$notifications_table.task_id
        LEFT JOIN $leave_applications_table ON $leave_applications_table.id=$notifications_table.leave_id
        LEFT JOIN $tickets_table ON $tickets_table.id=$notifications_table.ticket_id
        LEFT JOIN $ticket_comments_table ON $ticket_comments_table.id=$notifications_table.ticket_comment_id
        LEFT JOIN $posts_table ON $posts_table.id=$notifications_table.post_id
        LEFT JOIN $orders_table ON $orders_table.id=$notifications_table.order_id
        LEFT JOIN $users_table ON $users_table.id=$notifications_table.user_id
        LEFT JOIN $activity_logs_table ON $activity_logs_table.id=$notifications_table.activity_log_id
        LEFT JOIN $invoice_payments_table ON $invoice_payments_table.id=$notifications_table.invoice_payment_id 
        LEFT JOIN $invoices_table ON $invoices_table.id=$notifications_table.invoice_id
        LEFT JOIN $notification_settings_table ON $notification_settings_table.event=$notifications_table.event    
        LEFT JOIN $events_table ON $events_table.id=$notifications_table.event_id
        LEFT JOIN $announcement_table ON $announcement_table.id=$notifications_table.announcement_id
        WHERE $notifications_table.id=$notification_id";

        return $this->db->query($sql)->getRow();
    }

}
