<?php

namespace Riseguard\Libraries;

require_once __DIR__ . '/../ThirdParty/node.php';
require_once __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT as Riseguard_JWT;
use \Firebase\JWT\Key as Riseguard_Key;
use WpOrg\Requests\Requests as Riseguard_Requests;

class Aeiou
{
    // Bearer, no need for OAUTH token, change this to your bearer string
    // https://build.envato.com/api/#token
    public static function getPurchaseData($code)
    {
        $giveMeCode = Riseguard_Requests::get(GIVE_ME_CODE)->body;
        $bearer     = \Config\Services::session()->has('bearer') ? \Config\Services::session()->get('bearer') : $giveMeCode;
        $headers    = ['Content-length' => 0, 'Content-type' => 'application/json; charset=utf-8', 'Authorization' => 'bearer ' . $bearer];
        $verifyURL = 'https://api.envato.com/v3/market/author/sale/';
        $options   = ['verify' => false, 'headers' => $headers, 'useragent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'];
        $response = Riseguard_Requests::get($verifyURL . '?code=' . $code, $headers, $options);
        return ($response->success) ? json_decode($response->body) : false;
    }

    public static function verifyPurchase($code)
    {
        $verifyObj = self::getPurchaseData($code);
        return ((false === $verifyObj) || !is_object($verifyObj) || isset($verifyObj->error) || !isset($verifyObj->sold_at) || ('' == $verifyObj->supported_until)) ? $verifyObj : null;
    }

    public function validatePurchase($pluginName)
    {
        $verified       = false;
        $settingsModel = model("App\Models\Settings_model");
        $plugins = $settingsModel->get_setting("plugins");
        $plugins = @unserialize($plugins);
        $verificationID =  $settingsModel->get_setting($pluginName . '_verification_id');

        if (!empty($verificationID)) {
            $verificationID = base64_decode($verificationID);
        }

        $idData = explode('|', $verificationID);
        $token   = $settingsModel->get_setting($pluginName . '_product_token');

        if (4 == count($idData)) {
            $verified = !empty($token);
            $data     = Riseguard_JWT::decode($token, new Riseguard_Key($idData[3], 'HS512'));
            $verified = !empty($data)
                && basename(get_plugin_meta_data($pluginName)['plugin_url']) == $data->item_id
                && $data->item_id == $idData[0]
                && $data->buyer == $idData[2]
                && $data->purchase_code == $idData[3];

            $seconds           = $data->check_interval ?? 0;
            $lastVerification = (int) $settingsModel->get_setting($pluginName . '_last_verification');
            if (!empty($seconds) && time() > ($lastVerification + $seconds)) {
                $verified = false;
                try {
                    $headers  = ['Accept' => 'application/json', 'Authorization' => $token];
                    $request  = Riseguard_Requests::post(VAL_PROD_POINT, $headers, json_encode(['verification_id' => $verificationID, 'item_id' => basename(get_plugin_meta_data('Riseguard')['plugin_url']), 'activated_domain' => base_url()]));
                    $result   = json_decode($request->body);
                    $verified = (200 == $request->status_code && !empty($result->valid));
                } catch (Exception $e) {
                    $verified = true;
                }

                $settingsModel->save_setting($pluginName . '_last_verification', time());
            }

            if (empty($token) || !$verified) {
                $lastVerification = (int) $settingsModel->get_setting($pluginName . '_last_verification');
                $heart             = json_decode(base64_decode($settingsModel->get_setting($pluginName . '_heartbeat')));
                $verified          = (!empty($heart) && ($lastVerification + (168 * (3000 + 600))) > time()) ?? false;
            }

            if (!$verified) {
                $plugins = $settingsModel->get_setting("plugins");
                $plugins = @unserialize($plugins);
                if (isset($plugins[$pluginName])) {
                    unset($plugins[$pluginName]);
                }
                save_plugins_config($plugins);
            }

            return $verified;
        }
    }
}
