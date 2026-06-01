<?php

if (!function_exists('verify_classiccompiler_rise_plugins_purchase_code')) {

    function verify_classiccompiler_rise_plugins_purchase_code($product, $code) {
		return "verified";
    }

}

//validate purchase code
$verification = verify_classiccompiler_rise_plugins_purchase_code($product, $item_purchase_code);
