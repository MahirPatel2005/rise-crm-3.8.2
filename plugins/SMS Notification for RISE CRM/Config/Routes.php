<?php

namespace Config;

$routes = Services::routes();

$plugin_namespace = ['namespace' => 'SMS\Controllers'];

$routes->get('sms', 'Sms::index', $plugin_namespace);
$routes->post('sms/(:any)', 'Sms::$1', $plugin_namespace);
$routes->get('sms/(:any)', 'Sms::$1', $plugin_namespace);

$routes->get('sms_updates', 'Sms_Updates::index', $plugin_namespace);
$routes->get('sms_updates/(:any)', 'Sms_Updates::$1', $plugin_namespace);
