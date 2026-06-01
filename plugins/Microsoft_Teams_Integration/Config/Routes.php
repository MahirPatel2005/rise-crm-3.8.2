<?php

namespace Config;

$routes = Services::routes();

$routes->get('microsoft_team_meetings', 'Microsoft_Team_Meetings::index', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
$routes->get('microsoft_team_meetings/(:any)', 'Microsoft_Team_Meetings::$1', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
$routes->add('microsoft_team_meetings/(:any)', 'Microsoft_Team_Meetings::$1', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
$routes->post('microsoft_team_meetings/(:any)', 'Microsoft_Team_Meetings::$1', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);

$routes->get('microsoft_teams_integration_settings', 'Microsoft_Teams_Integration_settings::index', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
$routes->get('microsoft_teams_integration_settings/(:any)', 'Microsoft_Teams_Integration_settings::$1', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
$routes->post('microsoft_teams_integration_settings/(:any)', 'Microsoft_Teams_Integration_settings::$1', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);

$routes->get('microsoft_teams_integration_updates', 'Microsoft_Teams_Integration_Updates::index', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
$routes->get('microsoft_teams_integration_updates/(:any)', 'Microsoft_Teams_Integration_Updates::$1', ['namespace' => 'Microsoft_Teams_Integration\Controllers']);
