<?php

namespace Riseguard\Libraries;

require_once __DIR__ . '/../ThirdParty/node.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Aeiou.php';

use Firebase\JWT\JWT as Riseguard_JWT;
use Firebase\JWT\Key as Riseguard_Key;
use WpOrg\Requests\Requests as Riseguard_Requests;

class Apiinit
{
    public static function the_da_vinci_code($pluginName)
    {
        $settingsModel = model("App\Models\Settings_model");
        $verificationID = $settingsModel->get_setting($pluginName . '_verification_id');

        $verificationID =  !empty($verificationID) ? base64_decode($verificationID) : "";
        $token = $settingsModel->get_setting($pluginName . '_product_token');

        $idData         = explode('|', $verificationID);
        $verified        = !((empty($verificationID)) || (4 != count($idData)));

        if (4 === count($idData) && !is_null($token)) {
            $verified = !empty($token);
            try {
                $data = Riseguard_JWT::decode($token, new Riseguard_Key($idData[3], 'HS512'));
                if (!empty($data)) {
                    $verified = basename(get_plugin_meta_data($pluginName)['plugin_url']) == $data->item_id && $data->item_id == $idData[0] && $data->buyer == $idData[2] && $data->purchase_code == $idData[3];
                }
            } catch (Exception $e) {
                $verified = false;
            }

            $lastVerification = (int) $settingsModel->get_setting($pluginName . '_last_verification');
            $seconds           = $data->check_interval ?? 0;

            if (!empty($seconds) && time() > ($lastVerification + $seconds)) {
                $verified = false;
                try {
                    $request = Riseguard_Requests::post(VAL_PROD_POINT, ['Accept' => 'application/json', 'Authorization' => $token], json_encode(['verification_id' => $verificationID, 'item_id' => basename(get_plugin_meta_data($pluginName)['plugin_url']), 'activated_domain' => base_url()]));
                    $status  = $request->status_code;
                    if ((500 <= $status && $status <= 599) || 404 == $status) {
                        $settingsModel->save_setting($pluginName . '_heartbeat', base64_encode(json_encode(['status' => $status, 'id' => $token, 'end_point' => VAL_PROD_POINT])));
                        $verified = false;
                    } else {
                        $result   = json_decode($request->body);
                        $verified = !empty($result->valid);
                        if ($verified) {
                            $dbprefix = get_db_prefix();
                            $db = db_connect('default');
                            $builder = $db->table($dbprefix . "settings");
                            $builder->where('setting_name', $pluginName . "_heartbeat")->delete();
                        }
                    }
                } catch (Exception $e) {
                    $verified = false;
                }
                $settingsModel->save_setting($pluginName . '_last_verification', time());
            }
        }

        if (!$verified) {
            $plugins = $settingsModel->get_setting("plugins");
            $plugins = @unserialize($plugins);
            if (isset($plugins[$pluginName])) {
                unset($plugins[$pluginName]);
            }
            save_plugins_config($plugins);

            $settingsModel->save_setting("plugins", serialize($plugins));
        }
        return $verified;
    }

    public static function ease_of_mind($pluginName)
    {
        if (!function_exists($pluginName . '_actLib')) {
            $settingsModel = model("App\Models\Settings_model");
            $plugins = $settingsModel->get_setting("plugins");

            $plugins = @unserialize($plugins);
            $plugins[$pluginName] = "deactivated";
            save_plugins_config($plugins);

            $settingsModel->save_setting("plugins", serialize($plugins));
        }
    }

    public static function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public static function pre_validate($pluginName, $code = '')
    {
        if (empty($code)) {
            return ['status' => false, 'message' => 'Purchase key is required'];
        }

        $settingsModel = model("App\Models\Settings_model");
        $plugins = $settingsModel->get_setting("plugins");
        $all_activated = @unserialize($plugins);
        if (!($all_activated && is_array($all_activated))) {
            $all_activated = array();
        }
        foreach ($all_activated as $active_plugin => $value) {
            $verificationID = $settingsModel->get_setting($active_plugin . '_verification_id');
            if (!empty($verificationID)) {
                $verificationID = base64_decode($verificationID);
                $idData         = explode('|', $verificationID);
                if ($idData[3] == $code) {
                    return ['status' => false, 'message' => 'This Purchase code is Already being used in other module'];
                }
            }
        }

        $envatoRes = \Riseguard\Libraries\Aeiou::getPurchaseData($code);

        if (empty($envatoRes)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }
        if (!empty($envatoRes->error)) {
            return ['status' => false, 'message' => $envatoRes->description];
        }
        if (empty($envatoRes->sold_at)) {
            return ['status' => false, 'message' => 'Sold time for this code is not found'];
        }
        if ((false === $envatoRes) || !is_object($envatoRes) || isset($envatoRes->error) || !isset($envatoRes->sold_at)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }

        if (basename(get_plugin_meta_data($pluginName)['plugin_url']) != $envatoRes->item->id) {
            return ['status' => false, 'message' => 'Purchase key is not valid'];
        }

        $request = \Config\Services::request();
        $agentData = $request->getUserAgent();

        $data['user_agent']        = $agentData->getBrowser() . ' ' . $agentData->getVersion();
        $data['activated_domain']  = base_url();
        $data['requested_at']      = date('Y-m-d H:i:s');
        $data['ip']                = self::getUserIP();
        $data['os']                = $agentData->getPlatform();
        $data['purchase_code']     = $code;
        $data['envato_res']        = $envatoRes;
        $data['installed_version'] = get_plugin_meta_data($pluginName)['version'];
        $data                      = json_encode($data);

        try {
            $headers = ['Accept' => 'application/json'];
            $request = Riseguard_Requests::post(REG_PROD_POINT, $headers, $data);
            if ($request->status_code >= 500 || 404 == $request->status_code) {
                $settingsModel->save_setting($pluginName . '_verification_id', '');
                $settingsModel->save_setting($pluginName . '_last_verification', time());
                $settingsModel->save_setting($pluginName . '_heartbeat', base64_encode(json_encode(["status" => $request->status_code, "id" => $code, "end_point" => REG_PROD_POINT])));

                return ['status' => true];
            }

            $response = json_decode($request->body);
            if (200 != $response->status) {
                return ['status' => false, 'message' => $response->message];
            }

            $return = $response->data ?? [];
            if (!empty($return)) {
                $settingsModel->save_setting($pluginName . '_verification_id', base64_encode($return->verification_id));
                $settingsModel->save_setting($pluginName . '_last_verification', time());
                $settingsModel->save_setting($pluginName . '_product_token', $return->token);

                $dbprefix = get_db_prefix();
                $db = db_connect('default');

                $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='" . $pluginName . "_heartbeat';";
                $db->query($sql_query);

                return ['status' => true];
            }
        } catch (Exception $e) {
            $settingsModel->save_setting($pluginName . '_verification_id', '');
            $settingsModel->save_setting($pluginName . '_last_verification', time());
            $settingsModel->save_setting($pluginName . '_heartbeat', base64_encode(json_encode(["status" => $request->status_code, "id" => $code, "end_point" => REG_PROD_POINT])));

            return ['status' => true];
        }

        return ['status' => false, 'message' => 'Something went wrong'];
    }
}
