<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: RiseGuard
  Plugin URL: https://codecanyon.net/item/riseguard-the-powerful-security-toolset-plugin-for-rise-crm/48776904
  Description: The powerful security toolset plugin for RISE CRM
  Version: 1.0.0
  Requires at least: 3.5.2
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Libraries/Apiinit.php';

use WpOrg\Requests\Requests as Riseguard_Requests;
use Riseguard\Libraries\Apiinit;

app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
    $riseguard_submenu = [];

    $riseguard_submenu[] = [
        'name'  => 'dashboard',
        'url'   => 'riseguard/riseguard_log',
        'class' => '',
    ];

    $riseguard_submenu[] = [
        'name'  => 'settings',
        'url'   => 'riseguard/settings',
        'class' => '',
    ];

    $sidebar_menu['riseguard'] = [
        'name'     => 'riseguard',
        'url'      => 'riseguard',
        'class'    => 'minimize-2',
        'position' => 3,
        'submenu'  => $riseguard_submenu,
    ];

    return $sidebar_menu;
});

register_installation_hook("Riseguard", function ($item_purchase_code) {
    include PLUGINPATH . "Riseguard/install/do_install.php";
});

register_uninstallation_hook('Riseguard', function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name` IN ('Riseguard_verification_id', 'Riseguard_last_verification', 'Riseguard_product_token', 'Riseguard_heartbeat');";
    $db->query($sql_query);
});

app_hooks()->add_action('app_hook_layout_main_view_extension', function () {
    echo '
        <script src="' . base_url(PLUGIN_URL_PATH . 'Riseguard/assets/js/riseguard.js?v=' . get_setting('app_version')) . '"></script>
    ';
});

app_hooks()->add_action('app_hook_before_signout', function () {
    $request = request();
    $userAgent = $request->getUserAgent();
    $session = \Config\Services::session();

    // Set the user ID based on whether the user is a client or staff member
    $id = $session->get('user_id');

    // Get the user's device details (IP address, platform, and browser)
    $deviceDetails = [
        'ip_address' => getClientIp(),
        'platform'   => $userAgent->getPlatform(),
        'browser'    => $userAgent->getBrowser()
    ];

    // Set the data to update in the database (device details and is_logged_in status)
    $updateData = [
        'device_details' => serialize($deviceDetails),
        'is_logged_in'   => '0'
    ];

    // Update the user's data in the database
    $db = db_connect('default');
    $db->table('users')->where(['id' => $id])->update($updateData);
});

// hook for logout track => app_hook_before_signout
app_hooks()->add_action('app_hook_signin_extension', function () {
    echo '<div class="mt15"><a href="' . site_url('riseguard/reset_session') . '" title="">' . app_lang('reset_session') . '</a></div>';
});

app_hooks()->add_action("app_hook_after_cron_run", function () {
    // Get an array of expired staff members.
    $expiredStaff = getExpiredStaff();

    // If there are expired staff members, mark them as inactive in the database.
    if (!empty($expiredStaff)) {
        $expiredStaffIds = array_column($expiredStaff, 'id');
        $db = db_connect('default');
        $user_builder = $db->table("users");
        $staffDetails = $user_builder->whereIn('id', $expiredStaffIds)->update(['disable_login' => '1']);
    }
});

app_hooks()->add_filter("app_filter_email_templates", function ($templates_array) {
    $templates_array["riseguard"] = array(
        "unrecognized_login_detected_staff" => array("USER_FIRST_NAME", "USER_LAST_NAME", "USER_LOGIN_EMAIL", "RECIPIENTS_EMAIL_ADDRESS", "ISP", "IP_ADDRESS", "COUNTRY"),
        "multiple_failed_login_attempts" => array("USER_FIRST_NAME", "USER_LAST_NAME", "USER_LOGIN_EMAIL", "RECIPIENTS_EMAIL_ADDRESS", "ISP", "IP_ADDRESS", "COUNTRY"),
        "unrecognized_login_detected_to_admin" => array("USER_FIRST_NAME", "USER_LAST_NAME", "USER_LOGIN_EMAIL", "RECIPIENTS_EMAIL_ADDRESS", "ISP", "IP_ADDRESS", "COUNTRY"),
    );
    return $templates_array;
});