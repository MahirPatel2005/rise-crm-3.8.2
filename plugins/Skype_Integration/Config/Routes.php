<?php

namespace Config;

$routes = Services::routes();

$routes->get('skype_meetings', 'Skype_Meetings::index', ['namespace' => 'Skype_Integration\Controllers']);
$routes->get('skype_meetings/(:any)', 'Skype_Meetings::$1', ['namespace' => 'Skype_Integration\Controllers']);
$routes->add('skype_meetings/(:any)', 'Skype_Meetings::$1', ['namespace' => 'Skype_Integration\Controllers']);
$routes->post('skype_meetings/(:any)', 'Skype_Meetings::$1', ['namespace' => 'Skype_Integration\Controllers']);

$routes->get('skype_integration_settings', 'Skype_Integration_settings::index', ['namespace' => 'Skype_Integration\Controllers']);
$routes->get('skype_integration_settings/(:any)', 'Skype_Integration_settings::$1', ['namespace' => 'Skype_Integration\Controllers']);
$routes->post('skype_integration_settings/(:any)', 'Skype_Integration_settings::$1', ['namespace' => 'Skype_Integration\Controllers']);

$routes->get('skype_integration_updates', 'Skype_Integration_Updates::index', ['namespace' => 'Skype_Integration\Controllers']);
$routes->get('skype_integration_updates/(:any)', 'Skype_Integration_Updates::$1', ['namespace' => 'Skype_Integration\Controllers']);
