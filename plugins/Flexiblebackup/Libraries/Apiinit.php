<?php

namespace Flexiblebackup\Libraries;

require_once __DIR__.'/../ThirdParty/node.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Aeiou.php';
use Firebase\JWT\JWT as Flexiblebackup_JWT;
use Firebase\JWT\Key as Flexiblebackup_Key;
use WpOrg\Requests\Requests as Flexiblebackup_Requests;

class Apiinit
{
    public static function the_da_vinci_code($plugin_name)
    {
        $Settings_model  = model("App\Models\Settings_model");
        $verification_id = $Settings_model->get_setting($plugin_name.'_verification_id');

        $verification_id =  !empty($verification_id) ? base64_decode($verification_id) : '';
        $token           = $Settings_model->get_setting($plugin_name.'_product_token');

        $id_data         = explode('|', $verification_id);
        $verified        = !((empty($verification_id)) || (4 != \count($id_data)));

        if (4 === \count($id_data) && null !== $token) {
            $verified = !empty($token);
            try {
                $data =Flexiblebackup_JWT::decode($token, new Flexiblebackup_Key($id_data[3], 'HS512'));
                if (!empty($data)) {
                    $verified = basename(get_plugin_meta_data($plugin_name)['plugin_url']) == $data->item_id && $data->item_id == $id_data[0] && $data->buyer == $id_data[2] && $data->purchase_code == $id_data[3];
                }
            } catch (Exception $e) {
                $verified = false;
            }

            $last_verification = (int) $Settings_model->get_setting($plugin_name.'_last_verification');
            $seconds           = $data->check_interval ?? 0;

            if (!empty($seconds) && time() > ($last_verification + $seconds)) {
                $verified = false;
                try {
                    $request = Flexiblebackup_Requests::post(CTL_VAL_PROD_POINT, ['Accept' => 'application/json', 'Authorization' => $token], json_encode(['verification_id' => $verification_id, 'item_id' => basename(get_plugin_meta_data($plugin_name)['plugin_url']), 'activated_domain' => base_url()]));
                    $status  = $request->status_code;
                    if ((500 <= $status && $status <= 599) || 404 == $status) {
                        $Settings_model->save_setting($plugin_name.'_heartbeat', base64_encode(json_encode(['status' => $status, 'id' => $token, 'end_point' => CTL_VAL_PROD_POINT])));
                        $verified = false;
                    } else {
                        $result   = json_decode($request->body);
                        $verified = !empty($result->valid);
                        if ($verified) {
                            $dbprefix = get_db_prefix();
                            $db       = db_connect('default');
                            $builder  = $db->table($dbprefix.'settings');
                            $builder->where('setting_name', $plugin_name.'_heartbeat')->delete();
                        }
                    }
                } catch (Exception $e) {
                    $verified = false;
                }
                $Settings_model->save_setting($plugin_name.'_last_verification', time());
            }
        }

        if (!$verified) {
            $plugins = $Settings_model->get_setting('plugins');
            $plugins = @unserialize($plugins);
            if (isset($plugins[$plugin_name])) {
                unset($plugins[$plugin_name]);
            }
            save_plugins_config($plugins);

            $Settings_model->save_setting('plugins', serialize($plugins));
        }

        return $verified;
    }

    public static function ease_of_mind($plugin_name)
    {
        if (!\function_exists($plugin_name.'_actLib')) {
            $Settings_model        = model("App\Models\Settings_model");
            $plugins               = $Settings_model->get_setting('plugins');
            $plugins               = @unserialize($plugins);
            $plugins[$plugin_name] = 'deactivated';
            save_plugins_config($plugins);

            $Settings_model->save_setting('plugins', serialize($plugins));
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

    public static function pre_validate($plugin_name, $code = '')
    {
        if (empty($code)) {
            return ['status' => false, 'message' => 'Purchase key is required'];
        }

        $Settings_model = model("App\Models\Settings_model");
        $plugins        = $Settings_model->get_setting('plugins');
        $all_activated  = @unserialize($plugins);

        if (!($all_activated && \is_array($all_activated))) {
            $all_activated = [];
        }

        foreach ($all_activated as $active_plugin => $value) {
            $verification_id = $Settings_model->get_setting($active_plugin.'_verification_id');
            if (!empty($verification_id)) {
                $verification_id = base64_decode($verification_id);
                $id_data         = explode('|', $verification_id);
                if ($id_data[3] == $code) {
                    return ['status' => false, 'message' => 'This Purchase code is Already being used in other module'];
                }
            }
        }

        $envato_res = \Flexiblebackup\Libraries\Aeiou::getPurchaseData($code);

        if (empty($envato_res)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }
        if (!empty($envato_res->error)) {
            return ['status' => false, 'message' => $envato_res->description];
        }
        if (empty($envato_res->sold_at)) {
            return ['status' => false, 'message' => 'Sold time for this code is not found'];
        }
        if ((false === $envato_res) || !\is_object($envato_res) || isset($envato_res->error) || !isset($envato_res->sold_at)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }

        if (basename(get_plugin_meta_data($plugin_name)['plugin_url']) != $envato_res->item->id) {
            return ['status' => false, 'message' => 'Purchase key is not valid'];
        }

        $request    = \Config\Services::request();
        $agent_data = $request->getUserAgent();

        $data['user_agent']        = $agent_data->getBrowser().' '.$agent_data->getVersion();
        $data['activated_domain']  = base_url();
        $data['requested_at']      = date('Y-m-d H:i:s');
        $data['ip']                = self::getUserIP();
        $data['os']                = $agent_data->getPlatform();
        $data['purchase_code']     = $code;
        $data['envato_res']        = $envato_res;
        $data['installed_version'] = get_plugin_meta_data($plugin_name)['version'];
        $data                      = json_encode($data);

        try {
            $headers = ['Accept' => 'application/json'];
            $request = Flexiblebackup_Requests::post(CTL_REG_PROD_POINT, $headers, $data);
            if ($request->status_code >= 500 || 404 == $request->status_code) {
                $Settings_model->save_setting($plugin_name.'_verification_id', '');
                $Settings_model->save_setting($plugin_name.'_last_verification', time());
                $Settings_model->save_setting($plugin_name.'_heartbeat', base64_encode(json_encode(['status' => $request->status_code, 'id' => $code, 'end_point' => CTL_REG_PROD_POINT])));

                return ['status' => true];
            }

            $response = json_decode($request->body);
            if (200 != $response->status) {
                return ['status' => false, 'message' => $response->message];
            }

            $return = $response->data ?? [];
            if (!empty($return)) {
                $Settings_model->save_setting($plugin_name.'_verification_id', base64_encode($return->verification_id));
                $Settings_model->save_setting($plugin_name.'_last_verification', time());
                $Settings_model->save_setting($plugin_name.'_product_token', $return->token);

                $dbprefix = get_db_prefix();
                $db       = db_connect('default');

                $sql_query = 'DELETE FROM `'.$dbprefix.'settings` WHERE `'.$dbprefix."settings`.`setting_name`='".$plugin_name."_heartbeat';";
                $db->query($sql_query);

                return ['status' => true];
            }
        } catch (Exception $e) {
            $Settings_model->save_setting($plugin_name.'_verification_id', '');
            $Settings_model->save_setting($plugin_name.'_last_verification', time());
            $Settings_model->save_setting($plugin_name.'_heartbeat', base64_encode(json_encode(['status' => $request->status_code, 'id' => $code, 'end_point' => CTL_REG_PROD_POINT])));

            return ['status' => true];
        }

        return ['status' => false, 'message' => 'Something went wrong'];
    }
}
