<?php

namespace Config;

$routes = Services::routes();

$riseGuardNamespace = ['namespace' => 'Riseguard\Controllers'];

$routes->match(['get', 'post'], 'riseguard/settings', 'RiseguardController::settings', $riseGuardNamespace);
$routes->match(['get', 'post'], 'riseguard/addLoginExpiry', 'RiseguardController::addLoginExpiry', $riseGuardNamespace);
$routes->match(['get', 'post'], 'riseguard/add_ip_email', 'RiseguardController::addIpOrEmailToBlacklist', $riseGuardNamespace);
$routes->match(['get', 'post'], 'riseguard/riseguard_log', 'RiseguardController::riseGuardLog', $riseGuardNamespace);
$routes->match(['get', 'post'], 'riseguard/remove_staff_expiry/(:any)', 'RiseguardController::removeStaffExpiry/$1', $riseGuardNamespace);

$routes->match(['get', 'post'], 'riseguard/remove_ip_email/(:any)/(:any)', 'RiseguardController::removeIpOrEmailFromBlacklist/$1/$2', $riseGuardNamespace);

$routes->post('riseguard/riseguardlog_table', 'RiseguardController::riseguardLogTable', $riseGuardNamespace);
$routes->post('riseguard/user_expiry_table', 'RiseguardController::staffExpiryTable', $riseGuardNamespace);
$routes->post('riseguard/blacklistip_table/(:any)', 'RiseguardController::blackListIpTable/$1', $riseGuardNamespace);

$routes->match(['get', 'post'], 'riseguard/reset_session', 'RiseguardAuthController::resetSession', $riseGuardNamespace);

$routes->match(['get', 'post'], 'riseguard/riseguard_clear_log', 'RiseguardController::riseguardClearLog', $riseGuardNamespace);
