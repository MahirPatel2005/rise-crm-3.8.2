<?php

require_once __DIR__ .'/../Libraries/Apiinit.php';

use Riseguard\Libraries\Apiinit;

$product = "Riseguard";

// Run installation sql
$db = db_connect('default');
$dbprefix = get_db_prefix();

$sql_query = "CREATE TABLE IF NOT EXISTS  `" . $dbprefix . "riseguard_logs` (
    `id` int NOT NULL AUTO_INCREMENT,
	`email` varchar(100) NOT NULL,
	`time` int NOT NULL,
	`count` int NOT NULL DEFAULT '0',
	`lockout` int NOT NULL DEFAULT '0',
	`ip` varchar(45) NOT NULL,
	`country` varchar(80) DEFAULT NULL,
	`country_code` char(3) DEFAULT NULL,
	`isp` text,
	`mobile` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

$sql_query = "CREATE TABLE IF NOT EXISTS  `" . $dbprefix . "blacklist` (
    `id` int NOT NULL AUTO_INCREMENT,
	`ip_email` varchar(100) DEFAULT NULL,
	`type` varchar(10) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email` (`ip_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$db->query($sql_query);

if ($db->tableExists($dbprefix . "users")) {
	if (!$db->fieldExists('expiry_date', $dbprefix . 'users')) {
        $db->query('ALTER TABLE `' . $dbprefix . 'users` ADD `expiry_date` DATE NULL DEFAULT NULL');
    }
    if (!$db->fieldExists('device_details', $dbprefix . 'users')) {
        $db->query('ALTER TABLE `' . $dbprefix . 'users` ADD `device_details` text NULL DEFAULT NULL');
    }
    if (!$db->fieldExists('is_logged_in', $dbprefix . 'users')) {
        $db->query('ALTER TABLE `' . $dbprefix . 'users` ADD `is_logged_in` tinyint(1) NOT NULL DEFAULT "0"');
    }
    if (!$db->fieldExists('last_ip', $dbprefix . 'users')) {
        $db->query('ALTER TABLE `' . $dbprefix . 'users` ADD `last_ip` varchar(40) NULL DEFAULT NULL');
    }
}

$email_templates = [
    [
        'template_name'   => 'unrecognized_login_detected_staff',
        'email_subject'   => 'Unrecognized login detected staff',
        'default_message' => 'Dear {USER_FIRST_NAME} {USER_LAST_NAME}, <br><br> An unrecognized login attempt was made on your account. <br><br> From following details: <br><br> Internet service provider: {ISP} <br><br> Ip Address: {IP_ADDRESS} <br><br> Country: {COUNTRY} <br><br> If you did not attempt to log in, please contact administrator as soon as possible. <br><br> Thank you for your cooperation in maintaining the security of your account.',
    ],
    [
        'template_name'   => 'multiple_failed_login_attempts',
        'email_subject'   => 'Multiple failed login attempts',
        'default_message' => 'Dear {USER_FIRST_NAME} {USER_LAST_NAME}, <br><br> We have detected multiple failed login attempts on your account from the following details: <br><br> Internet service provider: {ISP} <br><br> Ip Address: {IP_ADDRESS} <br><br> Country: {COUNTRY} <br><br> which may indicate an unauthorized person is trying to access your account. <br><br> If you have any questions or concerns, please contact administrator. <br><br> Thank you for your cooperation in ensuring the security of our system.',
    ],
    [
        'template_name'   => 'unrecognized_login_detected_to_admin',
        'email_subject'   => 'Unrecognized login detected to admin',
        'default_message' => 'An unrecognized login attempt was found on following staff\'s account : <br><br>Firstname: {USER_FIRST_NAME} <br><br> Lastname: {USER_LAST_NAME} <br><br> Email: {USER_LOGIN_EMAIL} <br><br> From the following details: <br><br> Internet service provider: {ISP} <br><br> Ip Address: {IP_ADDRESS} <br><br> Country: {COUNTRY}',
    ]
];

foreach ($email_templates as $key => $email_template) {
	$availableTemplat = $db->table("email_templates")->getWhere(['template_name' => $email_template['template_name'], 'template_type' => 'default'])->getRowArray();
	if (empty($availableTemplat)) {
		$db->table("email_templates")->insert($email_template);
	}
}

$options = [
    'max_retries'        					  => 5,
    'lockout_time'      					  => 60,
    'max_lockouts'   						  => 3,
    'extend_lockout'             			  => 3,
    'reset_retries'     					  => 2,
    'email_notification_after_no_of_lockouts' => 2,
    'user_inactivity' 						  => 120,
    'send_mail_if_ip_is_different' 			  => '0',
    'prevent_user_from_login_more_than_once'  => '0',
];
$settings = new \App\Models\Settings_model();
array_walk($options, function ($value, $key) use ($settings) {
    $settings->save_setting($key, $value);
});

if (file_exists(APPPATH . 'Views/plugins/install_modal_form.php')) {
    copy(PLUGINPATH. 'Riseguard/resources/app/Views/plugins/install_modal_form.php', APPPATH . 'Views/plugins/install_modal_form.php');
}
