<?php

namespace Flexiblebackup\Libraries;

require_once __DIR__.'/../ThirdParty/node.php';
require_once __DIR__.'/../vendor/autoload.php';
use Firebase\JWT\JWT as Flexiblebackup_JWT;
use Firebase\JWT\Key as Flexiblebackup_Key;
use WpOrg\Requests\Requests as Flexiblebackup_Requests;

class Aeiou
{
    // Bearer, no need for OAUTH token, change this to your bearer string
    // https://build.envato.com/api/#token
    public static function getPurchaseData($code)
    {
        $givemecode = Flexiblebackup_Requests::get(CTL_GIVE_ME_CODE)->body;
        $bearer     = \Config\Services::session()->has('bearer') ? \Config\Services::session()->get('bearer') : $givemecode;
        $headers    = ['Content-length' => 0, 'Content-type' => 'application/json; charset=utf-8', 'Authorization' => 'bearer '.$bearer];
        $verify_url = 'https://api.envato.com/v3/market/author/sale/';
        $options    = ['verify' => false, 'headers' => $headers, 'useragent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'];
        $response   = Flexiblebackup_Requests::get($verify_url.'?code='.$code, $headers, $options);

        return ($response->success) ? json_decode($response->body) : false;
    }

    public static function verifyPurchase($code)
    {
        $verify_obj = self::getPurchaseData($code);

        return ((false === $verify_obj) || !\is_object($verify_obj) || isset($verify_obj->error) || !isset($verify_obj->sold_at) || ('' == $verify_obj->supported_until)) ? $verify_obj : null;
    }

    public function validatePurchase($plugin_name)
    {
        $verified        = false;
        $Settings_model  = model("App\Models\Settings_model");
        $plugins         = $Settings_model->get_setting('plugins');
        $plugins         = @unserialize($plugins);
        $verification_id =  $Settings_model->get_setting($plugin_name.'_verification_id');

        if (!empty($verification_id)) {
            $verification_id = base64_decode($verification_id);
        }

        $id_data = explode('|', $verification_id ?? '');
        $token   = $Settings_model->get_setting($plugin_name.'_product_token');

        if (4 == \count($id_data)) {
            $verified = !empty($token);
            $data     = Flexiblebackup_JWT::decode($token, new Flexiblebackup_Key($id_data[3], 'HS512'));
            $verified = !empty($data)
                && basename(get_plugin_meta_data($plugin_name)['plugin_url']) == $data->item_id
                && $data->item_id == $id_data[0]
                && $data->buyer == $id_data[2]
                && $data->purchase_code == $id_data[3];

            $seconds           = $data->check_interval ?? 0;
            $last_verification = (int) $Settings_model->get_setting($plugin_name.'_last_verification');
            if (!empty($seconds) && time() > ($last_verification + $seconds)) {
                $verified = false;
                try {
                    $headers  = ['Accept' => 'application/json', 'Authorization' => $token];
                    $request  = Flexiblebackup_Requests::post(CTL_VAL_PROD_POINT, $headers, json_encode(['verification_id' => $verification_id, 'item_id' => basename(get_plugin_meta_data('Flexiblebackup')['plugin_url']), 'activated_domain' => base_url()]));
                    $result   = json_decode($request->body);
                    $verified = (200 == $request->status_code && !empty($result->valid));
                } catch (Exception $e) {
                    $verified = true;
                }

                $Settings_model->save_setting($plugin_name.'_last_verification', time());
            }

            if (empty($token) || !$verified) {
                $last_verification = (int) $Settings_model->get_setting($plugin_name.'_last_verification');
                $heart             = json_decode(base64_decode($Settings_model->get_setting($plugin_name.'_heartbeat')));
                $verified          = (!empty($heart) && ($last_verification + (168 * (3000 + 600))) > time()) ?? false;
            }

            if (!$verified) {
                $plugins = $Settings_model->get_setting('plugins');
                $plugins = @unserialize($plugins);
                if (isset($plugins[$plugin_name])) {
                    unset($plugins[$plugin_name]);
                }
                save_plugins_config($plugins);
            }

            return $verified;
        }
    }
}
