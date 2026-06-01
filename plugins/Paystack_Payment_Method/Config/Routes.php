<?php

namespace Config;

$routes = Services::routes();

$routes->get('paystack_payment_method', 'Paystack_Payment_Method::index', ['namespace' => 'Paystack_Payment_Method\Controllers']);
$routes->get('paystack_payment_method/(:any)', 'Paystack_Payment_Method::$1', ['namespace' => 'Paystack_Payment_Method\Controllers']);
$routes->add('paystack_payment_method/(:any)', 'Paystack_Payment_Method::$1', ['namespace' => 'Paystack_Payment_Method\Controllers']);
$routes->post('paystack_payment_method/(:any)', 'Paystack_Payment_Method::$1', ['namespace' => 'Paystack_Payment_Method\Controllers']);

$routes->get('paystack_payment_method_updates', 'Paystack_Payment_Method_Updates::index', ['namespace' => 'Paystack_Payment_Method\Controllers']);
$routes->get('paystack_payment_method_updates/(:any)', 'Paystack_Payment_Method_Updates::$1', ['namespace' => 'Paystack_Payment_Method\Controllers']);
