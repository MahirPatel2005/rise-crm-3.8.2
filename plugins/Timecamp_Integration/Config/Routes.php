<?php

namespace Config;

$routes = Services::routes();

$routes->get('timecamp_integration', 'Timecamp_Integration::index', ['namespace' => 'Timecamp_Integration\Controllers']);
$routes->get('timecamp_integration/(:any)', 'Timecamp_Integration::$1', ['namespace' => 'Timecamp_Integration\Controllers']);
$routes->add('timecamp_integration/(:any)', 'Timecamp_Integration::$1', ['namespace' => 'Timecamp_Integration\Controllers']);
$routes->post('timecamp_integration/(:any)', 'Timecamp_Integration::$1', ['namespace' => 'Timecamp_Integration\Controllers']);

$routes->get('timecamp_integration_updates', 'Timecamp_Integration_Updates::index', ['namespace' => 'Timecamp_Integration\Controllers']);
$routes->get('timecamp_integration_updates/(:any)', 'Timecamp_Integration_Updates::$1', ['namespace' => 'Timecamp_Integration\Controllers']);