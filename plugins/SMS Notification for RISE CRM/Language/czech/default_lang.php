<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Povolit SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Telefonní číslo Twilio";
$lang["sms_send_test_sms"] = "Pošlete testovací SMS";
$lang["sms_notifications"] = "SMS upozornění";
$lang["sms_notification_settings"] = "Nastavení upozornění na SMS";
$lang["sms_notification_template"] = "Šablona oznámení SMS";
$lang["sms_templates"] = "SMS šablony";
$lang["sms_template_name"] = "Název šablony";
$lang["sms_edit_sms_template"] = "Upravit šablonu SMS";

/* messages */
$lang["sms_twilio_help_message"] = "Integrace služby Twilio SMS je jednosměrné zasílání zpráv, což znamená, že vaši zákazníci nebudou moci na SMS odpovídat. Telefonní čísla musí být ve formátu %s. Kliknutím na %s se dozvíte více o formátování telefonních čísel.";
$lang["sms_twilio_phone_no_help_message"] = "Telefonní čísla musí být ve formátu %s. Jinak nemůže dostávat SMS.";
$lang["sms_notification_edit_instruction"] = "Poznámka: Upozornit na bude sledovat nastavení oznámení aplikace.";

$lang["sms_send_test_sms_successfull_message"] = "Testovací SMS byla úspěšně odeslána!";
$lang["sms_send_test_sms_error_message"] = "Chyba! Nelze se spojit s Twilio pomocí přihlašovacích údajů.";

$lang["sms_info_message"] = "Chcete -li dostávat oznámení SMS, musí být povolena webová oznámení.";
$lang["sms_twilio_user_phone_no_help_message"] = "V telefonním čísle uživatele/klienta použijte %s formát (<b> +14155552671 </b>). V opačném případě nemusí upozornění SMS fungovat.";

return $lang;
