<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Włącz SMS-y";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Numer telefonu Twilio";
$lang["sms_send_test_sms"] = "Wyślij testową wiadomość SMS";
$lang["sms_notifications"] = "Powiadomienie SMS";
$lang["sms_notification_settings"] = "Ustawienia powiadomień SMS";
$lang["sms_notification_template"] = "Szablon powiadomienia SMS";
$lang["sms_templates"] = "Szablony SMS";
$lang["sms_template_name"] = "Nazwa szablonu";
$lang["sms_edit_sms_template"] = "Edytuj szablon SMS";

/* messages */
$lang["sms_twilio_help_message"] = "Integracja Twilio SMS to jednokierunkowe przesyłanie wiadomości, co oznacza, że Twoi klienci nie będą mogli odpowiadać na SMS-y. Numery telefonów muszą być w formacie %s. Kliknij %s, aby dowiedzieć się więcej o formatowaniu numerów telefonów.";
$lang["sms_twilio_phone_no_help_message"] = "Numery telefonów muszą być w formacie %s. W przeciwnym razie nie otrzyma SMS-a.";
$lang["sms_notification_edit_instruction"] = "Uwaga: Powiadom do będzie postępować zgodnie z ustawieniami powiadomień aplikacji.";

$lang["sms_send_test_sms_successfull_message"] = "Testowy SMS został pomyślnie wysłany!";
$lang["sms_send_test_sms_error_message"] = "Błąd! Nie można połączyć się z Twilio przy użyciu poświadczeń.";

$lang["sms_info_message"] = "Aby otrzymywać powiadomienia SMS, powiadomienia sieciowe muszą być włączone.";
$lang["sms_twilio_user_phone_no_help_message"] = "Użyj formatu %s (<b>+14155552671</b>) w numerze telefonu użytkownika/klienta. W przeciwnym razie powiadomienie SMS może nie działać.";

return $lang;
