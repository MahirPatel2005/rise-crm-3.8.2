<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Habilitar SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Número de telefone do Twilio";
$lang["sms_send_test_sms"] = "Enviar SMS de teste";
$lang["sms_notifications"] = "Notificação SMS";
$lang["sms_notification_settings"] = "Configurações de notificação de SMS";
$lang["sms_notification_template"] = "Modelo de notificação de SMS";
$lang["sms_templates"] = "Modelos SMS";
$lang["sms_template_name"] = "Nome do modelos";
$lang["sms_edit_sms_template"] = "Editar modelo de SMS";

/* messages */
$lang["sms_twilio_help_message"] = "A integração do Twilio SMS é uma mensagem unilateral, o que significa que seus clientes não poderão responder ao SMS. Os números de telefone devem estar no formato %s. Clique em %s para ler mais sobre como os números de telefone devem ser formatados.";
$lang["sms_twilio_phone_no_help_message"] = "Os números de telefone devem estar no formato %s. Caso contrário, ele/ela não pode receber SMS.";
$lang["sms_notification_edit_instruction"] = "Nota: Notificar para seguirá as configurações de notificação do aplicativo.";

$lang["sms_send_test_sms_successfull_message"] = "O SMS de teste foi enviado com sucesso!";
$lang["sms_send_test_sms_error_message"] = "Erro! Não é possível conectar com o Twilio usando as credenciais.";

$lang["sms_info_message"] = "Para receber notificações por SMS, as notificações da web devem estar ativadas.";
$lang["sms_twilio_user_phone_no_help_message"] = "Use o formato %s (<b> +14155552671 </b>) no número de telefone do usuário / cliente. Caso contrário, a notificação por SMS pode não funcionar.";

return $lang;
