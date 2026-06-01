<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Sms inschakelen";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Twilio Telefoonnummer";
$lang["sms_send_test_sms"] = "Test-sms verzenden";
$lang["sms_notifications"] = "SMS-melding";
$lang["sms_notification_settings"] = "Instellingen voor SMS-meldingen";
$lang["sms_notification_template"] = "SMS-notificatie sjabloon";
$lang["sms_templates"] = "SMS-sjablonen";
$lang["sms_template_name"] = "Sjabloon naam";
$lang["sms_edit_sms_template"] = "SMS-sjabloon bewerken";

/* messages */
$lang["sms_twilio_help_message"] = "Twilio SMS-integratie is eenrichtingsberichten, wat betekent dat uw klanten de sms niet kunnen beantwoorden. Telefoonnummers moeten de notatie %s hebben. Klik op %s om meer te lezen hoe telefoonnummers moeten worden opgemaakt.";
$lang["sms_twilio_phone_no_help_message"] = "Telefoonnummers moeten de notatie %s hebben. Anders kan hij/zij geen sms ontvangen.";
$lang["sms_notification_edit_instruction"] = "Opmerking: Als u een melding geeft, volgt u de instellingen voor app-meldingen.";

$lang["sms_send_test_sms_successfull_message"] = "Test-sms is succesvol verzonden!";
$lang["sms_send_test_sms_error_message"] = "Fout! Kan geen verbinding maken met de Twilio met behulp van de inloggegevens.";

$lang["sms_info_message"] = "Om sms-meldingen te ontvangen, moeten webmeldingen zijn ingeschakeld.";
$lang["sms_twilio_user_phone_no_help_message"] = "Gebruik het %s-formaat (<b>+14155552671</b>) in het telefoonnummer van de gebruiker/klant. Anders werkt de sms-melding mogelijk niet.";

return $lang;
