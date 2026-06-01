<?php

namespace Config;

$routes = Services::routes();

$routes->get('jitsi_meetings', 'Jitsi_Meetings::index', ['namespace' => 'Jitsi_Integration\Controllers']);
$routes->get('jitsi_meetings/(:any)', 'Jitsi_Meetings::$1', ['namespace' => 'Jitsi_Integration\Controllers']);
$routes->add('jitsi_meetings/(:any)', 'Jitsi_Meetings::$1', ['namespace' => 'Jitsi_Integration\Controllers']);
$routes->post('jitsi_meetings/(:any)', 'Jitsi_Meetings::$1', ['namespace' => 'Jitsi_Integration\Controllers']);

$routes->get('jitsi_integration_settings', 'Jitsi_Integration_settings::index', ['namespace' => 'Jitsi_Integration\Controllers']);
$routes->get('jitsi_integration_settings/(:any)', 'Jitsi_Integration_settings::$1', ['namespace' => 'Jitsi_Integration\Controllers']);
$routes->post('jitsi_integration_settings/(:any)', 'Jitsi_Integration_settings::$1', ['namespace' => 'Jitsi_Integration\Controllers']);

$routes->get('jitsi_integration_updates', 'Jitsi_Integration_Updates::index', ['namespace' => 'Jitsi_Integration\Controllers']);
$routes->get('jitsi_integration_updates/(:any)', 'Jitsi_Integration_Updates::$1', ['namespace' => 'Jitsi_Integration\Controllers']);
