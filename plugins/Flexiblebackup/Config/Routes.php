<?php

namespace Config;

$routes = Services::routes();

$flexiblebackup_namespace = ['namespace' => 'Flexiblebackup\Controllers'];

$routes->match(['get', 'post'], 'flexiblebackup/settings/(:any)', 'FlexiblebackupController::settings/$1', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/list_backups', 'FlexiblebackupController::listBackups', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/backup_modal', 'FlexiblebackupController::loadBackupView', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/handle_backup_actions/(:any)/(:any)(/(:any))?', 'FlexiblebackupController::handleBackupActions/$1/$2/$3', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/restore_backup_files', 'FlexiblebackupController::restoreBackupFiles', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/remove_backup_directory/(:num)/(:any)', 'FlexiblebackupController::removeBackupDirectory/$1/$2', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/download_backup_file/(:any)/(:any)', 'FlexiblebackupController::downloadBackupFile/$1/$2', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/execute_remote_backup_upload', 'FlexiblebackupController::executeRemoteBackupUpload', $flexiblebackup_namespace);
$routes->match(['get', 'post'], 'flexiblebackup/backup', 'FlexiblebackupController::backupView', $flexiblebackup_namespace);

// Post routes
$routes->post('flexiblebackup/perform_backup', 'FlexiblebackupController::performBackup', $flexiblebackup_namespace);
$routes->post('flexiblebackup/save_settings', 'FlexiblebackupController::saveSettings', $flexiblebackup_namespace);
