<?php

if ($notification->task_id && $notification->task_title) {
    echo app_lang("task") . ": #$notification->task_id - " . $notification->task_title . "\n";
}

if ($notification->payment_invoice_id) {
    echo to_currency($notification->payment_amount, $notification->client_currency_symbol) . "  -  " . get_invoice_id($notification->payment_invoice_id) . "\n";
}

if ($notification->ticket_id && $notification->ticket_title) {
    echo get_ticket_id($notification->ticket_id) . " - " . $notification->ticket_title . "\n";
}

if ($notification->leave_id && $notification->leave_start_date) {
    $leave_date = format_to_date($notification->leave_start_date, FALSE);
    if ($notification->leave_start_date != $notification->leave_end_date) {
        $leave_date = sprintf(app_lang('start_date_to_end_date_format'), format_to_date($notification->leave_start_date, FALSE), format_to_date($notification->leave_end_date, FALSE));
    }
    echo app_lang("date") . ": " . $leave_date . "\n";
}

if ($notification->project_comment_id && $notification->project_comment_title && !strpos($notification->project_comment_title, "</a>")) {
    echo app_lang("comment") . ": " . convert_mentions($notification->project_comment_title, false) . "\n";
}

if ($notification->project_file_id && $notification->project_file_title) {
    echo app_lang("file") . ": " . remove_file_prefix($notification->project_file_title) . "\n";
}


if ($notification->project_id && $notification->project_title) {
    echo app_lang("project") . ": " . $notification->project_title . "\n";
}

if ($notification->estimate_id) {
    echo get_estimate_id($notification->estimate_id) . "\n";
}

if ($notification->order_id) {
    echo get_order_id($notification->order_id) . "\n";
}

if ($notification->event_title) {
    echo app_lang("event") . ": " . $notification->event_title . "\n";
}

if ($notification->announcement_title) {
    echo app_lang("title") . ": " . $notification->announcement_title . "\n";
}

if ($notification->post_id && $notification->posts_title) {
    echo app_lang("comment") . ": " . $notification->posts_title . "\n";
}