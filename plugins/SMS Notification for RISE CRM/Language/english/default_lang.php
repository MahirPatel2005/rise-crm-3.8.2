<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Enable SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Twilio Phone Number";
$lang["sms_send_test_sms"] = "Send test SMS";
$lang["sms_notifications"] = "SMS notification";
$lang["sms_notification_settings"] = "SMS notification settings";
$lang["sms_notification_template"] = "SMS notification template";
$lang["sms_templates"] = "SMS Templates";
$lang["sms_template_name"] = "Template name";
$lang["sms_edit_sms_template"] = "Edit SMS template";

/* messages */
$lang["sms_twilio_help_message"] = "Twilio SMS integration is one way messaging, means that your customers won't be able to reply to the SMS. Phone numbers must be in format %s. Click %s to read more how phone numbers should be formatted.";
$lang["sms_twilio_phone_no_help_message"] = "Phone numbers must be in format %s. Otherwise he/she can't get SMS.";
$lang["sms_notification_edit_instruction"] = "Note: Notify to will follow app notification settings.";

$lang["sms_send_test_sms_successfull_message"] = "Test SMS has been sent successfully!";
$lang["sms_send_test_sms_error_message"] = "Error! Can't connect with the Twilio using the credentials.";

$lang["sms_info_message"] = "To receive SMS notifications, web notifications must be enabled.";
$lang["sms_twilio_user_phone_no_help_message"] = "Please use %s format (<b>+14155552671</b>) in user/client phone number. Otherwise the SMS notification may will not work.";

return $lang;
