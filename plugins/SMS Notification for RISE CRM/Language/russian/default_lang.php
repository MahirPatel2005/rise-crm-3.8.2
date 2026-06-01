<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Включить SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Номер телефона Twilio";
$lang["sms_send_test_sms"] = "Отправить тестовое SMS";
$lang["sms_notifications"] = "SMS-уведомление";
$lang["sms_notification_settings"] = "Настройки SMS-уведомлений";
$lang["sms_notification_template"] = "Шаблон SMS-уведомления";
$lang["sms_templates"] = "SMS шаблоны";
$lang["sms_template_name"] = "Имя Шаблона";
$lang["sms_edit_sms_template"] = "Редактировать шаблон SMS";

/* messages */
$lang["sms_twilio_help_message"] = "Интеграция Twilio SMS - это односторонний обмен сообщениями, что означает, что ваши клиенты не смогут отвечать на SMS. Номера телефонов должны быть в формате %s. Щелкните %s, чтобы узнать больше о форматировании номеров телефонов.";
$lang["sms_twilio_phone_no_help_message"] = "Номера телефонов должны быть в формате %s. В противном случае он не сможет получить SMS.";
$lang["sms_notification_edit_instruction"] = "заметка. Уведомите, чтобы следовать настройкам уведомлений приложения.";

$lang["sms_send_test_sms_successfull_message"] = "Тестовое СМС успешно отправлено!";
$lang["sms_send_test_sms_error_message"] = "Ошибка! Не удается подключиться к Twilio с использованием учетных данных.";

$lang["sms_info_message"] = "Для получения SMS-уведомлений необходимо включить веб-уведомления.";
$lang["sms_twilio_user_phone_no_help_message"] = "Используйте формат %s (<b> +14155552671 </b>) в номере телефона пользователя / клиента. В противном случае SMS-уведомление может не работать.";

return $lang;
