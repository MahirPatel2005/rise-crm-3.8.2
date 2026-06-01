<?php

namespace Flexiblebackup\Config;

use CodeIgniter\Events\Events;
use App\Models\Users_model;

Events::on('pre_system', function () {
    helper(['riseguard', 'general']);
});

Events::on('post_controller_constructor', function () {
    define('PREVENT_USER_FROM_LOGIN_MORE_THAN_ONCE', get_setting('prevent_user_from_login_more_than_once'));
    define('USER_INACTIVITY_TIME', (int) get_setting('user_inactivity'));
    define('MAX_RETRIES', !empty(get_setting('max_retries')) ? (int) get_setting('max_retries') : 5);
    define('EMAIL_NOTIFICATION_AFTER_NO_OF_LOCKOUTS', !empty(get_setting('email_notification_after_no_of_lockouts')) ? (int) get_setting('email_notification_after_no_of_lockouts') : 1);
    define('EXTEND_LOCKOUT', !empty(get_setting('extend_lockout')) ? (int) get_setting('extend_lockout') : 1);
    define('LOCKOUT_TIME', !empty(get_setting('lockout_time')) ? (int) get_setting('lockout_time') : 1);
    define('RESET_RETRIES', !empty(get_setting('reset_retries')) ? (int) get_setting('reset_retries') : 1);

    $router = service('router');

    $request = request();
    $userAgent = $request->getUserAgent();
    $requestUrl = $request->getUri();

    $session = \Config\Services::session();

    $controller = $router->controllerName();
    $method = $router->methodName();

    if (PREVENT_USER_FROM_LOGIN_MORE_THAN_ONCE == 1 && !empty($session->get('user_id'))) {
        $db = db_connect('default');
        $admin = $db->table('users')->select('id')->getWhere(['id' => $session->get('user_id'), 'is_admin' => 1])->getNumRows();
        $user = $db->table('users')->getWhere(['id' => $session->get('user_id')])->getRowArray();

        if ($admin == 0) {
            $previousSessionDetails = unserialize($user['device_details']);
            if (
                $previousSessionDetails['browser'] !== $userAgent->getBrowser() ||
                $previousSessionDetails['platform'] !== $userAgent->getPlatform() ||
                $previousSessionDetails['ip_address'] !== getClientIp()
            ) {
                $userModel = new Users_model();
                $userModel->sign_out();
            }
        }
    }

    if ($controller == "\App\Controllers\Signin" && $method == "authenticate") {
        // before login method (for login lockout)
        $email = $request->getPost("email");
        if (empty($email)) {
            return;
        }

        $db = db_connect('default');
        $user_builder = $db->table("users");
        $isAdminEmail = $user_builder->select("id")->getWhere(array('email' => $email, 'is_admin' => 1))->getNumRows();
        $lastLogin = $db->table('users')->getWhere(['email' => $email])->getRowArray();
        if ($isAdminEmail) {
            if (!empty($lastLogin) && $lastLogin['last_ip'] !== getClientIp()) {
                $ipDetails = json_decode(file_get_contents("http://ip-api.com/json/" . getClientIp()));

                $ipLogData = [
                    'country' => '',
                    'isp'     => '',
                    'ip'      => getClientIp()
                ];

                // If the IP details were successfully obtained, extract the relevant information into an array
                if ($ipDetails->status == 'success') {
                    $ipLogData = [
                        'country' => $ipDetails->country,
                        'isp'     => $ipDetails->isp,
                        'ip'      => getClientIp()
                    ];
                }
                sendEmailRiseguard('unrecognized_login_detected_staff', $lastLogin, $ipLogData);
            }
            $db->table('users')->where(['email' => $email])->update(['last_ip' => getClientIp()]);
            return;
        }

        // Check if the user's IP address or email is blocked
        $ipAddress = getClientIp();

        if (isIpBlocked($ipAddress) || isEmailBlocked($email)) {
            // If the user's IP address or email is blocked, set an alert and redirect
            $session->setFlashdata("flag", true);
            $session->setFlashdata("signin_validation_errors", [app_lang("login_access_denied")]);
            app_redirect('signin');
        }

        if (!empty($lastLogin) && $lastLogin['last_ip'] !== $ipAddress) {
            $ipDetails = json_decode(file_get_contents("http://ip-api.com/json/" . getClientIp()));

            $ipLogData = [
                'country' => '',
                'isp'     => '',
                'ip'      => getClientIp()
            ];

            // If the IP details were successfully obtained, extract the relevant information into an array
            if ($ipDetails->status == 'success') {
                $ipLogData = [
                    'country' => $ipDetails->country,
                    'isp'     => $ipDetails->isp,
                    'ip'      => getClientIp()
                ];
            }
            sendEmailRiseguard('unrecognized_login_detected_staff', $lastLogin, $ipLogData);
            if (get_setting('send_mail_if_ip_is_different') == '1') {
                $adminDetails = $db->table("users")->getWhere(['is_admin' => 1])->getRowArray();
                sendEmailRiseguard('unrecognized_login_detected_to_admin', $adminDetails, $ipLogData);
            }
        }

        $session->setFlashdata("signin_email", $email);
        $data = isLockedUser($email, getClientIp());
        $staffDetails = getStaffDetailsByEmail($email);
        if (!empty($staffDetails)) {
            if (!empty($data) || !empty(MAX_RETRIES)) {

                // Check if the user has reached the maximum number of retries
                if (count($data) % (int)MAX_RETRIES == 0) {
                    $lockoutCycleCount = getLockoutCycleCount(count($data));

                    if (empty(EMAIL_NOTIFICATION_AFTER_NO_OF_LOCKOUTS)) {
                        return;
                    }

                    if ($lockoutCycleCount >= (int) EMAIL_NOTIFICATION_AFTER_NO_OF_LOCKOUTS) {
                        $ipDetails = json_decode(file_get_contents("http://ip-api.com/json/" . getClientIp()));

                        $ipLogData = [
                            'country' => '',
                            'isp'     => '',
                            'ip'      => getClientIp()
                        ];

                        // If the IP details were successfully obtained, extract the relevant information into an array
                        if ($ipDetails->status == 'success') {
                            $ipLogData = [
                                'country' => $ipDetails->country,
                                'isp'     => $ipDetails->isp,
                                'ip'      => getClientIp()
                            ];
                        }

                        /**
                         * The below logic is for the following feature:
                         * Send email notification to staff after specified number of lockouts.
                         */
                        $sessionLockoutCycle = $session->get('session_lockout_cycle') ?? 0;
                        if ($lockoutCycleCount !== $sessionLockoutCycle) {
                            // mail sending logic here: Pending
                            $mailSent = sendEmailRiseguard('multiple_failed_login_attempts', $staffDetails, $ipLogData);
                            if ($mailSent) {
                                $session->set('session_lockout_cycle', $lockoutCycleCount);
                            }
                        }
                    }

                    // Check if the user has reached the maximum number of lockouts
                    if ($lockoutCycleCount >= get_setting('max_lockouts')) {
                        // Get the time of the last attempt
                        $firstvalue = (!empty($data) ? $data[array_key_first($data)]->time : 0);
                        $last_attempt_time = $firstvalue;
                        // Check if the user is still within the extended lockout time
                        if ($last_attempt_time > time() - (EXTEND_LOCKOUT * 3600)) {
                            // Set an alert message and redirect the user
                            $session->setFlashdata("flag", true);
                            $session->setFlashdata("signin_validation_errors", [app_lang("you_are_blocked_for") . EXTEND_LOCKOUT . " Hours"]);
                            app_redirect('signin');
                            exit;
                        }
                    }

                    // Get the time of the last attempt
                    $firstvalue = (!empty($data) ? $data[array_key_first($data)]->time : 0);
                    $last_attempt_time = (int)$firstvalue;
                    // Check if the user is still within the lockout time
                    if ($last_attempt_time > time() - (LOCKOUT_TIME * 60)) {
                        // Set an alert message and redirect the user
                        $session->setFlashdata("flag", true);
                        $session->setFlashdata("signin_validation_errors", [app_lang("you_are_blocked_for") . LOCKOUT_TIME . " Minutes"]);
                        app_redirect('signin');
                        exit;
                    }
                }
            }

            // Get the user's device details (IP address, platform, and browser)
            $deviceDetails = [
                'ip_address' => getClientIp(),
                'platform'   => $userAgent->getPlatform(),
                'browser'    => $userAgent->getBrowser()
            ];

            if (PREVENT_USER_FROM_LOGIN_MORE_THAN_ONCE == 1 && $staffDetails['is_logged_in'] == '1') {
                $previousSessionDetails = unserialize($staffDetails['device_details']);

                if (
                    $previousSessionDetails['browser'] !== $deviceDetails['browser'] ||
                    $previousSessionDetails['platform'] !== $deviceDetails['platform'] ||
                    $previousSessionDetails['ip_address'] !== $deviceDetails['ip_address']
                ) {
                    $userModel = new Users_model();
                    $userModel->sign_out();
                }
            }

            // Set the data to update in the database (device details and is_logged_in status)
            $updateData = [
                'device_details' => serialize($deviceDetails),
                'is_logged_in'   => '1',
                'last_ip'        => getClientIp(),
            ];

            // Update the user's data in the database
            $db = db_connect('default');
            $db->table('users')->where(['id'=> $staffDetails['id']])->update($updateData);
        }
    }

    if ($controller == "\App\Controllers\Signin" && $method == "index" && $session->getFlashdata("flag") == false && !empty($session->getFlashdata("signin_validation_errors"))) {
        // login failed method
        $email = $session->getFlashdata("signin_email");
        $ipLogData = [];

        // Get the user's IP address and use the ip-api.com API to get additional IP details
        $ipAddress = getClientIp();
        $ipDetails = json_decode(file_get_contents("http://ip-api.com/json/" . $ipAddress));

        $ipLogData = [];

        // If the IP details were successfully obtained, extract the relevant information into an array
        if ($ipDetails->status == 'success') {
            $ipLogData = [
                'country'      => $ipDetails->country,
                'country_code' => $ipDetails->countryCode,
                'isp'          => $ipDetails->isp,
            ];
        }

        // Create an array containing the user's email and IP address
        $logData = [
            'email' => $email,
            'ip'    => $ipAddress,
            'time'  => time(),
            "mobile" => (int) $userAgent->isMobile(), // mobile device ch k ny
        ];

        // Insert the data into the 'riseguard_logs' table
        $db = db_connect('default');
        $builder = $db->table('riseguard_logs');
        $builder->insert(array_merge($logData, $ipLogData));
    }

    // for reset session page hook => app_hook_signin_extension
});
