<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "SMS aktivieren";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Twilio-Telefonnummer";
$lang["sms_send_test_sms"] = "Test-SMS senden";
$lang["sms_notifications"] = "SMS-Benachrichtigung";
$lang["sms_notification_settings"] = "Einstellungen für SMS-Benachrichtigungen";
$lang["sms_notification_template"] = "SMS-Benachrichtigung vorlage";
$lang["sms_templates"] = "SMS-Vorlagen";
$lang["sms_template_name"] = "Vorlagenname";
$lang["sms_edit_sms_template"] = "SMS-Vorlage bearbeiten";

/* messages */
$lang["sms_twilio_help_message"] = "Die Twilio-SMS-Integration ist ein Einweg-Messaging, dh Ihre Kunden können nicht auf die SMS antworten. Telefonnummern müssen das Format %s haben. Klicken Sie auf %s, um mehr darüber zu erfahren, wie Telefonnummern formatiert werden sollten.";
$lang["sms_twilio_phone_no_help_message"] = "Telefonnummern müssen das Format %s haben. Sonst kann er/sie keine SMS bekommen.";
$lang["sms_notification_edit_instruction"] = "Hinweis: Benachrichtigen an folgt den App-Benachrichtigungseinstellungen.";

$lang["sms_send_test_sms_successfull_message"] = "Test-SMS wurde erfolgreich versendet!";
$lang["sms_send_test_sms_error_message"] = "Fehler! Mit den Anmeldeinformationen kann keine Verbindung zum Twilio hergestellt werden.";

$lang["sms_info_message"] = "Um SMS-Benachrichtigungen zu erhalten, müssen Web-Benachrichtigungen aktiviert sein.";
$lang["sms_twilio_user_phone_no_help_message"] = "Bitte verwenden Sie das %s-Format (<b>+14155552671</b>) in der Telefonnummer des Benutzers/Kunden. Andernfalls funktioniert die SMS-Benachrichtigung möglicherweise nicht.";

return $lang;
