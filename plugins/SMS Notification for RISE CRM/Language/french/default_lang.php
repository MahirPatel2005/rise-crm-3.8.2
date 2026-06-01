<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Activer les SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Numéro de téléphone de Twilio";
$lang["sms_send_test_sms"] = "Envoyer un SMS test";
$lang["sms_notifications"] = "notification par SMS";
$lang["sms_notification_settings"] = "Paramètres de notification par SMS";
$lang["sms_notification_template"] = "modèle de notification par SMS";
$lang["sms_templates"] = "Modèles de SMS";
$lang["sms_template_name"] = "Nom du gabarit";
$lang["sms_edit_sms_template"] = "Modifier le modèle de SMS";

/* messages */
$lang["sms_twilio_help_message"] = "L'intégration de Twilio SMS est une messagerie unidirectionnelle, ce qui signifie que vos clients ne pourront pas répondre au SMS. Les numéros de téléphone doivent être au format %s. Cliquez sur %s pour en savoir plus sur la façon dont les numéros de téléphone doivent être formatés.";
$lang["sms_twilio_phone_no_help_message"] = "Les numéros de téléphone doivent être au format %s. Sinon, il ne peut pas recevoir de SMS.";
$lang["sms_notification_edit_instruction"] = "Remarque : Notifier à suivra les paramètres de notification de l'application.";

$lang["sms_send_test_sms_successfull_message"] = "Le SMS de test a été envoyé avec succès!";
$lang["sms_send_test_sms_error_message"] = "Erreur! Impossible de se connecter au Twilio avec les identifiants.";

$lang["sms_info_message"] = "Pour recevoir des notifications par SMS, les notifications Web doivent être activées.";
$lang["sms_twilio_user_phone_no_help_message"] = "Veuillez utiliser le format %s (<b>+14155552671</b>) dans le numéro de téléphone de l'utilisateur/client. Sinon, la notification par SMS peut ne pas fonctionner.";

return $lang;
