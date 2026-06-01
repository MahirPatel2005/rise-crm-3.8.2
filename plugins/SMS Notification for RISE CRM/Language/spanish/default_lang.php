<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Habilitar SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Número de teléfono de Twilio";
$lang["sms_send_test_sms"] = "Enviar SMS de prueba";
$lang["sms_notifications"] = "Notificación por SMS";
$lang["sms_notification_settings"] = "Configuración de notificaciones por SMS";
$lang["sms_notification_template"] = "Plantilla de notificación por SMS";
$lang["sms_templates"] = "Plantillas de SMS";
$lang["sms_template_name"] = "Nombre de la plantilla";
$lang["sms_edit_sms_template"] = "Editar plantilla de SMS";

/* messages */
$lang["sms_twilio_help_message"] = "La integración de Twilio SMS es mensajería unidireccional, lo que significa que sus clientes no podrán responder al SMS. Los números de teléfono deben tener el formato %s. Haga clic en %s para leer más sobre cómo se deben formatear los números de teléfono.";
$lang["sms_twilio_phone_no_help_message"] = "Los números de teléfono deben tener el formato %s. De lo contrario, no podrá recibir SMS.";
$lang["sms_notification_edit_instruction"] = "Nota: Notificar a seguirá la configuración de notificación de la aplicación.";

$lang["sms_send_test_sms_successfull_message"] = "¡El SMS de prueba se ha enviado correctamente!";
$lang["sms_send_test_sms_error_message"] = "¡Error! No se puede conectar con Twilio usando las credenciales.";

$lang["sms_info_message"] = "Para recibir notificaciones por SMS, las notificaciones web deben estar habilitadas.";
$lang["sms_twilio_user_phone_no_help_message"] = "Utilice el formato %s (<b> +14155552671 </b>) en el número de teléfono del usuario / cliente. De lo contrario, es posible que la notificación por SMS no funcione.";

return $lang;
