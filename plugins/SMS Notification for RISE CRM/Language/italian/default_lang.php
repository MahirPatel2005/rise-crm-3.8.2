<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Abilita SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Numero di telefono Twilio";
$lang["sms_send_test_sms"] = "Invia SMS di prova";
$lang["sms_notifications"] = "Notifica SMS";
$lang["sms_notification_settings"] = "Impostazioni di notifica SMS";
$lang["sms_notification_template"] = "Modello di notifica SMS";
$lang["sms_templates"] = "Modelli SMS";
$lang["sms_template_name"] = "Nome modello";
$lang["sms_edit_sms_template"] = "Modifica modello SMS";

/* messages */
$lang["sms_twilio_help_message"] = "L'integrazione di Twilio SMS è un messaggio unidirezionale, significa che i tuoi clienti non saranno in grado di rispondere agli SMS. I numeri di telefono devono essere nel formato %s. Fare clic su %s per ulteriori informazioni su come formattare i numeri di telefono.";
$lang["sms_twilio_phone_no_help_message"] = "I numeri di telefono devono essere nel formato %s. In caso contrario, lui/lei non può ottenere SMS.";
$lang["sms_notification_edit_instruction"] = "Nota: Notificare a seguirà le impostazioni di notifica dell'app.";

$lang["sms_send_test_sms_successfull_message"] = "L'SMS di prova è stato inviato con successo!";
$lang["sms_send_test_sms_error_message"] = "Errore! Impossibile connettersi con Twilio utilizzando le credenziali.";

$lang["sms_info_message"] = "Per ricevere notifiche SMS, le notifiche web devono essere abilitate.";
$lang["sms_twilio_user_phone_no_help_message"] = "Si prega di utilizzare il formato %s (<b>+141155552671</b>) nel numero di telefono dell'utente/cliente. In caso contrario, la notifica SMS potrebbe non funzionare.";

return $lang;
