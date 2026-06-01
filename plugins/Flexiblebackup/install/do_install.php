<?php

require_once __DIR__.'/../Libraries/Apiinit.php';
use Flexiblebackup\Libraries\Apiinit;

ini_set('max_execution_time', 300); //300 seconds

$product = 'Flexiblebackup';

// Run installation sql
$db       = db_connect('default');
$dbprefix = get_db_prefix();

if (!$db->tableExists($dbprefix.'flexiblebackup')) {
    $sql_query = 'CREATE TABLE IF NOT EXISTS `'.$dbprefix.'flexiblebackup` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `backup_name` varchar(255) NOT NULL,
        `backup_type` varchar(50) NOT NULL,
        `backup_data` varchar(255) NOT NULL,
        `datecreated` datetime NOT NULL,
        `uploaded_to_remote` tinyint NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
    $db->query($sql_query);
}

$options = [
    'flexiblebackup_enabled'     => 1,
    'files_backup_schedule'      => 1,
    'database_backup_schedule'   => 1,
    'remote_storage'             => '',
    'recent_backup_file'         => '',
    'recent_backup_database'     => '',
];
$settings = new \App\Models\Settings_model();
array_walk($options, function ($value, $key) use ($settings) {
    $settings->save_setting($key, $value);
});

if (file_exists(APPPATH.'Views/plugins/install_modal_form.php')) {
    copy(PLUGINPATH.'Flexiblebackup/resources/app/Views/plugins/install_modal_form.php', APPPATH.'Views/plugins/install_modal_form.php');
}
