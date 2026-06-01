<?php

if (!function_exists('verify_cc_rise_plugins_purchase_code')) {

    function verify_cc_rise_plugins_purchase_code($product, $code) {
        return "verified";
    }

}

//validate purchase code
$verification = verify_cc_rise_plugins_purchase_code($product, $item_purchase_code);
