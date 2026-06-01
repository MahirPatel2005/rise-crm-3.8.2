<?php

namespace Config;

$routes = Services::routes();

$routes->get('auto_attendance_settings', 'Auto_Attendance_settings::index', ['namespace' => 'Auto_Attendance\Controllers']);
$routes->get('auto_attendance_settings/(:any)', 'Auto_Attendance_settings::$1', ['namespace' => 'Auto_Attendance\Controllers']);
$routes->post('auto_attendance_settings/(:any)', 'Auto_Attendance_settings::$1', ['namespace' => 'Auto_Attendance\Controllers']);

$routes->get('auto_attendance_updates', 'Auto_Attendance_Updates::index', ['namespace' => 'Auto_Attendance\Controllers']);
$routes->get('auto_attendance_updates/(:any)', 'Auto_Attendance_Updates::$1', ['namespace' => 'Auto_Attendance\Controllers']);
