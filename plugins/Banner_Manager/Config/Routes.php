<?php

namespace Config;

$routes = Services::routes();

$routes->get('banner_manager', 'Banner_Manager::index', ['namespace' => 'Banner_Manager\Controllers']);
$routes->get('banner_manager/(:any)', 'Banner_Manager::$1', ['namespace' => 'Banner_Manager\Controllers']);
$routes->add('banner_manager/(:any)', 'Banner_Manager::$1', ['namespace' => 'Banner_Manager\Controllers']);
$routes->post('banner_manager/(:any)', 'Banner_Manager::$1', ['namespace' => 'Banner_Manager\Controllers']);

$routes->get('banner_manager_settings', 'Banner_Manager_settings::index', ['namespace' => 'Banner_Manager\Controllers']);
$routes->get('banner_manager_settings/(:any)', 'Banner_Manager_settings::$1', ['namespace' => 'Banner_Manager\Controllers']);
$routes->post('banner_manager_settings/(:any)', 'Banner_Manager_settings::$1', ['namespace' => 'Banner_Manager\Controllers']);

$routes->get('banner_manager_updates', 'Banner_Manager_Updates::index', ['namespace' => 'Banner_Manager\Controllers']);
$routes->get('banner_manager_updates/(:any)', 'Banner_Manager_Updates::$1', ['namespace' => 'Banner_Manager\Controllers']);
