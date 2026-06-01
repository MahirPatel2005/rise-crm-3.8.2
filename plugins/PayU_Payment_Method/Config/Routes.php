<?php

namespace Config;

$routes = Services::routes();

$routes->get('payu_payment_method', 'PayU_Payment_Method::index', ['namespace' => 'PayU_Payment_Method\Controllers']);
$routes->get('payu_payment_method/(:any)', 'PayU_Payment_Method::$1', ['namespace' => 'PayU_Payment_Method\Controllers']);
$routes->add('payu_payment_method/(:any)', 'PayU_Payment_Method::$1', ['namespace' => 'PayU_Payment_Method\Controllers']);
$routes->post('payu_payment_method/(:any)', 'PayU_Payment_Method::$1', ['namespace' => 'PayU_Payment_Method\Controllers']);

$routes->get('payu_payment_method_updates', 'PayU_Payment_Method_Updates::index', ['namespace' => 'PayU_Payment_Method\Controllers']);
$routes->get('payu_payment_method_updates/(:any)', 'PayU_Payment_Method_Updates::$1', ['namespace' => 'PayU_Payment_Method\Controllers']);
