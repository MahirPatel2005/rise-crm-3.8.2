<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: PayU Payment Method
  Description: PayU payment gateway for RISE CRM.
  Version: 1.0
  Requires at least: 2.8
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler
 */

//add payment method
app_hooks()->add_filter('app_filter_payment_method_settings', function($settings) {
    $settings["payu"] = array(
        array("name" => "pay_button_text", "text" => app_lang("pay_button_text"), "type" => "text", "default" => "PayU"),
        array("name" => "payu_testing_environment", "text" => app_lang("testing_environment"), "type" => "boolean", "default" => "0"),
        array("name" => "pos_id", "text" => "POS ID (pos_id)", "type" => "text", "default" => ""),
        array("name" => "second_key", "text" => "Second key (MD5)", "type" => "text", "default" => ""),
        array("name" => "oauth_client_id", "text" => "OAuth protocol - client_id", "type" => "text", "default" => ""),
        array("name" => "oauth_client_secret", "text" => "OAuth protocol - client_secret", "type" => "text", "default" => ""),
    );

    return $settings;
});

//add payment button
app_hooks()->add_action('app_hook_invoice_payment_extension', function($payment_method_variables) {
    if (get_array_value($payment_method_variables, "method_type") === "payu") {
        echo view("PayU_Payment_Method\Views\payment_form", $payment_method_variables);
    }
});

//add csrf exclude uri
app_hooks()->add_action('app_filter_app_csrf_exclude_uris', function($app_csrf_exclude_uris) {
    if (!in_array("payu_payment_method.*+", $app_csrf_exclude_uris)) {
        array_push($app_csrf_exclude_uris, "payu_payment_method.*+");
    }

    return $app_csrf_exclude_uris;
});

//installation: install dependencies
register_installation_hook("PayU_Payment_Method", function ($item_purchase_code) {
    include PLUGINPATH . "PayU_Payment_Method/install/do_install.php";
});

//uninstallation: remove data from database
register_uninstallation_hook("PayU_Payment_Method", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE IF EXISTS `" . $dbprefix . "payu_ipn`;";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "payment_methods` WHERE `" . $dbprefix . "payment_methods`.`type`='payu';";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='payu_payment_method_item_purchase_code';";
    $db->query($sql_query);
});

//activation: activate the payment method
register_activation_hook("PayU_Payment_Method", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "UPDATE `" . $dbprefix . "payment_methods` SET `deleted` = '0' WHERE `" . $dbprefix . "payment_methods`.`type`='payu';";
    $db->query($sql_query);
});

//deactivation: deactivate the payment method
register_deactivation_hook("PayU_Payment_Method", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "UPDATE `" . $dbprefix . "payment_methods` SET `deleted` = '1' WHERE `" . $dbprefix . "payment_methods`.`type`='payu';";
    $db->query($sql_query);
});

//update plugin
use PayU_Payment_Method\Controllers\PayU_Payment_Method_Updates;

register_update_hook("PayU_Payment_Method", function () {
    $update = new PayU_Payment_Method_Updates();
    return $update->index();
});