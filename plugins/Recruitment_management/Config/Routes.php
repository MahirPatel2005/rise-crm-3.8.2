<?php

namespace Config;

$routes = Services::routes();

$recruitment_management = ['namespace' => 'Recruitment_management\Controllers'];

$routes->get('recruitment_management', 'Recruitment_management::index', $recruitment_management);
$routes->post('recruitment_management/(:any)', 'Recruitment_management::$1', $recruitment_management);
$routes->get('recruitment_management/(:any)', 'Recruitment_management::$1', $recruitment_management);

$routes->get('recruitment_settings', 'Recruitment_settings::index', $recruitment_management);
$routes->post('recruitment_settings/(:any)', 'Recruitment_settings::$1', $recruitment_management);
$routes->get('recruitment_settings/(:any)', 'Recruitment_settings::$1', $recruitment_management);

$routes->get('recruitment_circular_templates', 'Recruitment_circular_templates::index', $recruitment_management);
$routes->post('recruitment_circular_templates/(:any)', 'Recruitment_circular_templates::$1', $recruitment_management);
$routes->get('recruitment_circular_templates/(:any)', 'Recruitment_circular_templates::$1', $recruitment_management);

$routes->get('recruitment_circulars', 'Recruitment_circulars::index', $recruitment_management);
$routes->post('recruitment_circulars/(:any)', 'Recruitment_circulars::$1', $recruitment_management);
$routes->get('recruitment_circulars/(:any)', 'Recruitment_circulars::$1', $recruitment_management);

$routes->get('recruitment_candidates', 'Recruitment_candidates::index', $recruitment_management);
$routes->post('recruitment_candidates/(:any)', 'Recruitment_candidates::$1', $recruitment_management);
$routes->get('recruitment_candidates/(:any)', 'Recruitment_candidates::$1', $recruitment_management);

$routes->get('recruitment_management_updates', 'Recruitment_management_Updates::index', $recruitment_management);
$routes->get('recruitment_management_updates/(:any)', 'Recruitment_management_Updates::$1', $recruitment_management);
