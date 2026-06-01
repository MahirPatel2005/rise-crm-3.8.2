<?php

namespace Riseguard\Controllers;

use App\Controllers\Security_Controller;

class RiseguardController extends Security_Controller {

    protected $Riseguard_model;

    function __construct() {
        parent::__construct();
        $this->Riseguard_model = model('Riseguard\Models\RiseguardModel');
        $this->settings = new \App\Models\Settings_model();
    }

    public function settings() {
        if ($postData = $this->request->getPost()) {
            foreach ($postData as $settingsKey => $settingsValue) {
                $this->validate_submitted_data(array(
                    $settingsKey => "required",
                ));
                $this->settings->save_setting($settingsKey, $settingsValue);
            }
            echo json_encode(["success" => true, 'message' => app_lang('settings_updated_successfully')]);
            return;
        }
        
        $data['title'] = app_lang('settings');
        $data['allStaff'] = $this->Riseguard_model->getAllStaff();
        return $this->template->rander('Riseguard\Views\guard_settings',$data);
    }

    public function riseGuardLog()
    {
        $data['title'] = app_lang('riseguard_log');
        return $this->template->rander('Riseguard\Views\riseguard_dashboard',$data);
    }

    public function riseguardLogTable()
    {
        $logs = $this->Riseguard_model->getAllLogs();
        $result = array_map([$this, '_makeRow'], $logs);

        echo json_encode(['data' => $result]);
    }

    public function _makeRow($data)
    {
        return [
            $data['id'],
            $data['email'],
            format_to_relative_time(date("Y-m-d H:i:s", $data['time'])),
            $data['ip'],
            $data['count'],
            $data['lockout'],
            $data['country'],
            $data['country_code'],
            $data['isp'],
            $data['mobile'],
        ];
    }

    public function addLoginExpiry()
    {
        $postData = $this->request->getPost();
        $result = $this->Riseguard_model->addStaffExpiry($postData);
        echo json_encode($result);
    }

    public function staffExpiryTable()
    {
        $expiry = $this->Riseguard_model->getStaffExpiry();
        $result = array_map(function($ex) {

            $options = '<a href="javascript:void(0)" class="btn btn-sm btn-primary btn-icon mr5" onclick="editStaffExpiryDate(' . $ex['id'] . ', \'' . $ex['expiry_date'] . '\')"><i data-feather="edit" class="icon-16"></i></a>';
            $options .= js_anchor('<i data-feather="x" class="icon-16"></i>', array('title' => app_lang('delete'), "class" => "delete btn btn-sm btn-danger btn-icon", "data-id" => $ex['id'], "data-action-url" => get_uri('riseguard/remove_staff_expiry/'.$ex['id']), "data-action" => "delete-confirmation"));
            return [
                $ex['first_name'].$ex['last_name'],
                $ex['expiry_date'],
                $options
            ];

        }, $expiry);

        echo json_encode(['data' => $result]);
    }

    public function blackListIpTable($type)
    {
        $ips = $this->Riseguard_model->getBlackListData($type);
        $result = array_map(function($ipData) {
            return [
                $ipData['id'],
                $ipData['ip_email'],
                js_anchor('<i data-feather="x" class="icon-16"></i>', array('title' => app_lang('delete'), "class" => "delete btn btn-sm btn-danger btn-icon", "data-id" => $ipData['id'], "data-action-url" => get_uri('riseguard/remove_ip_email/'. $ipData['type'] .'/'.$ipData['id']), "data-action" => "delete-confirmation"))
            ];
        }, $ips);

        echo json_encode(['data' => $result]);
    }

    public function addIpOrEmailToBlacklist()
    {
        $type = $this->request->getPost('type');
        // Get the IP addresses or an empty array if none is provided.
        $ip_email_list = $this->request->getPost('blacklist_ip') ?? $this->request->getPost('blacklist_email') ?? [];

        $ip_email_list = array_unique($ip_email_list);
        $isUnique = $this->Riseguard_model->isUniqueIpOrEmail($ip_email_list, $type);
        
        if (!$isUnique['success']) {
            foreach ($ip_email_list as $key => $ipEmail) {
                if (in_array($ipEmail, $isUnique['ipEmail'])) {
                    unset($ip_email_list[$key]);
                }
            }
        }

        $error_message = app_lang($type).app_lang('email_ot_ip_already_on_our_blacklist');
        $postData = [];

        // check for duplication
        foreach ($ip_email_list as $value) {
            if ($type == "ip") {
                $range = array_map('trim', explode('-', $value));
                if ( count( $range ) > 1 && (float)sprintf("%u", ip2long($range[0])) > (float)sprintf("%u", ip2long($range[1]))) {
                    $error_message .= $value . app_lang('is_invalid_ip_range');
                }

            }
            if ( '' != $value ) {
                $postData[] = [
                    'ip_email' => $value,
                    'type'     => $type
                ];
            }
        }

        if (!empty($postData)) {
            // Save the data to the database and return the result as a JSON encoded string.
            $res = $this->Riseguard_model->addIpOrEmailToBlacklist($postData);
            echo json_encode(['success' => $res['status'], 'message' => $res['message'], 'type' => $res['type']]);
            return;
        }

        echo json_encode(['success' => false, 'message' => $error_message, 'type' => $type]);
    }

    public function removeStaffExpiry($staffID)
    {
        $res = $this->Riseguard_model->removeStaffExpiry($staffID);
        echo json_encode([
            'success' => $res,
            'message' => ($res) ? app_lang('expiry_date_removed') : app_lang('something_went_wrong')
        ]);
    }

     /**
     * Removes a specified IP address or User from the blacklist.
     *
     * @param int $id The ID of the IP address or User to remove.
     * @return void
     */
    public function removeIpOrEmailFromBlacklist($category, $id)
    {
        $res = $this->Riseguard_model->removeIpOrEmailFromBlacklist($category, $id);
        echo json_encode([
            'success' => $res,
            'message' => ($category == 'ip') ? app_lang('ip_removed_from_blacklist') : app_lang('email_removed_from_blacklist')
        ]);
    }

    public function riseguardClearLog()
    {
        $res = $this->Riseguard_model->riseguardClearLog();
        echo json_encode([
            'success' => $res,
            'message' => ($res) ? app_lang('logs_cleared') : app_lang('something_went_wrong')
        ]);
    }

}
