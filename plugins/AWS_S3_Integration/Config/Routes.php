<?php

namespace Config;

$routes = Services::routes();

$routes->get('aws_s3_integration_settings', 'AWS_S3_Integration_settings::index', ['namespace' => 'AWS_S3_Integration\Controllers']);
$routes->get('aws_s3_integration_settings/(:any)', 'AWS_S3_Integration_settings::$1', ['namespace' => 'AWS_S3_Integration\Controllers']);
$routes->post('aws_s3_integration_settings/(:any)', 'AWS_S3_Integration_settings::$1', ['namespace' => 'AWS_S3_Integration\Controllers']);

$routes->get('aws_s3_integration_updates', 'AWS_S3_Integration_Updates::index', ['namespace' => 'AWS_S3_Integration\Controllers']);
$routes->get('aws_s3_integration_updates/(:any)', 'AWS_S3_Integration_Updates::$1', ['namespace' => 'AWS_S3_Integration\Controllers']);
