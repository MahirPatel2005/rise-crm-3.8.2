<?php

/* NOTE: DO NOT CHANGE THIS FILE. IF YOU WANT TO UPDATE THE LANGUAGE THEN COPY THIS FILE TO custom_lang.php AND UPDATE THERE */

/* common */
$lang["twilio"] = "Twilio";

/* settings */
$lang["sms_enable_sms"] = "Ενεργοποίηση SMS";
$lang["sms_twilio_account_sid"] = "Account SID";
$lang["sms_twilio_auth_token"] = "Auth Token";
$lang["sms_twilio_phone_number"] = "Αριθμός τηλεφώνου Twilio";
$lang["sms_send_test_sms"] = "Αποστολή δοκιμαστικών SMS";
$lang["sms_notifications"] = "Ειδοποίηση SMS";
$lang["sms_notification_settings"] = "Ρυθμίσεις ειδοποιήσεων SMS";
$lang["sms_notification_template"] = "Πρότυπο ειδοποίησης SMS";
$lang["sms_templates"] = "Πρότυπα SMS";
$lang["sms_template_name"] = "Όνομα προτύπου";
$lang["sms_edit_sms_template"] = "Επεξεργασία προτύπου SMS";

/* messages */
$lang["sms_twilio_help_message"] = "Η ενσωμάτωση του Twilio SMS είναι μονόδρομος, που σημαίνει ότι οι πελάτες σας δεν θα μπορούν να απαντήσουν στο SMS. Οι αριθμοί τηλεφώνου πρέπει να έχουν τη μορφή %s. Κάντε κλικ στο %s για να διαβάσετε περισσότερα πώς πρέπει να μορφοποιηθούν οι αριθμοί τηλεφώνου.";
$lang["sms_twilio_phone_no_help_message"] = "Οι αριθμοί τηλεφώνου πρέπει να έχουν τη μορφή %s. Διαφορετικά δεν μπορεί να λάβει SMS.";
$lang["sms_notification_edit_instruction"] = "Σημείωση: Το Notify to θα ακολουθήσει τις ρυθμίσεις ειδοποιήσεων εφαρμογής.";

$lang["sms_send_test_sms_successfull_message"] = "Το δοκιμαστικό SMS στάλθηκε με επιτυχία!";
$lang["sms_send_test_sms_error_message"] = "Λάθος! Δεν είναι δυνατή η σύνδεση με το Twilio χρησιμοποιώντας τα διαπιστευτήρια.";

$lang["sms_info_message"] = "Για να λαμβάνετε ειδοποιήσεις SMS, πρέπει να είναι ενεργοποιημένες οι ειδοποιήσεις ιστού.";
$lang["sms_twilio_user_phone_no_help_message"] = "Χρησιμοποιήστε τη μορφή %s (<b> +14155552671 </b>) στον αριθμό τηλεφώνου χρήστη/πελάτη. Διαφορετικά, η ειδοποίηση SMS μπορεί να μην λειτουργήσει.";

return $lang;
