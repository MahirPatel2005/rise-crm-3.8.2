<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Aktiver SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Twilio telefonnummer";
$lang["sms_send_test_sms"] = "Send test -SMS";
$lang["sms_notifications"] = "SMS -varsling";
$lang["sms_notification_settings"] = "Innstillinger for SMS -varsling";
$lang["sms_notification_template"] = "SMS -varslingsmal";
$lang["sms_templates"] = "SMS -maler";
$lang["sms_template_name"] = "Malnavn";
$lang["sms_edit_sms_template"] = "Rediger SMS -mal";

/* messages */
$lang["sms_twilio_help_message"] = "Twilio SMS -integrasjon er enveis meldinger, betyr at kundene dine ikke kan svare på SMS -en. Telefonnumre må være i formatet %s. Klikk %s for å lese mer om hvordan telefonnumre skal formateres.";
$lang["sms_twilio_phone_no_help_message"] = "Telefonnumre må være i formatet %s. Ellers kan han/hun ikke få SMS.";
$lang["sms_notification_edit_instruction"] = "Merk: Varsle til vil følge innstillingene for appvarsler.";

$lang["sms_send_test_sms_successfull_message"] = "Test SMS har blitt sendt!";
$lang["sms_send_test_sms_error_message"] = "Feil! Kan ikke koble til Twilio ved å bruke legitimasjonen.";

$lang["sms_info_message"] = "For å motta SMS-varslinger, må web-meldinger være aktivert.";
$lang["sms_twilio_user_phone_no_help_message"] = "Bruk %s -format (<b> +14155552671 </b>) i bruker-/klient -telefonnummer. Ellers fungerer ikke SMS -varselet.";

return $lang;
