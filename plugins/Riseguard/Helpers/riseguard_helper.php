<?php

use App\Models\Email_templates_model;

if (!function_exists('isLockedUser')) {
    function isLockedUser($email, $ipAddress)
    {
        $db = db_connect('default');
        $user_builder = $db->table("users");
        $log_builder = $db->table("riseguard_logs");

        // Staff Login
        $staff = $user_builder->select("id, last_online")->getWhere(['email' => $email])->getRow();

        if (!empty($staff->last_online)) {
            $user_builder->where('time >', strtotime($staff->last_online), FALSE);
        }

        $time_condition = !empty(RESET_RETRIES) ? RESET_RETRIES * 3600 : 3600;

        $res = $log_builder->where('time >', time() - ($time_condition), FALSE)->getWhere(['email' => $email, 'ip' => $ipAddress]);

        return $res->getResult();
    }
}

if (!function_exists('getStaffDetailsByEmail')) {
    function getStaffDetailsByEmail($email)
    {
        $db = db_connect('default');
        $user_builder = $db->table("users");
        $staffDetails = $user_builder->getWhere(['email' => $email])->getRowArray();

        return (!empty($staffDetails)) ? $staffDetails : false;
    }
}

if (!function_exists('getLockoutCycleCount')) {
    function getLockoutCycleCount($count)
    {
        $lockoutCycleCount = 0;
        // Loop through the records
        for ($i = 1; $i <= $count; $i++) {
            // Check if the current record is a multiple of maximun number of retries.
            if ($i % MAX_RETRIES == 0) {
                $lockoutCycleCount++; // Increment the lockoutCycleCount variable
            }
        }

        return $lockoutCycleCount;
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        $ipAddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
    }
}

/**
 * Check if an Email address is blocked
 *
 * @param string $ipAddress The IP address to check
 * @return bool
 */
if (!function_exists('isEmailBlocked')) {
    function isEmailBlocked($email)
    {
        // From Blocked emails list
        $db = db_connect('default');
        $blacklist = $db->table("blacklist");
        $blacklistEmails = $blacklist->getWhere(['ip_email' => $email, 'type' => 'email'])->getRowArray();

        return !empty($blacklistEmails);
    }
}

/**
 * Check if an IP address is blocked
 *
 * @param string $ipAddress The IP address to check
 * @return bool
 */
if (!function_exists('isIpBlocked')) {
    function isIpBlocked($ipAddress)
    {
        // From Blocked IP list
        $db = db_connect('default');
        $blacklist = $db->table("blacklist");
        $blacklistIps = $blacklist->getWhere(['type' => 'ip'])->getResultArray();
        $list = array_column($blacklistIps, 'ip_email');

        $match = false;
        foreach ($list as $item) {

            if (strpos($item, '/') !== false || strpos($item, '-') !== false || strpos($item, '*') !== false) {
                //the IP in the list is in one of the acceptable range formats
                $match = ipInRage($ipAddress, $item);
            } elseif ($ipAddress == $item) {
                $match = true;
            }

            //break as soon as a match is found
            if ($match) break;
        }

        return $match;
    }
}

if (!function_exists('ipInRage')) {
    function ipInRage($ip, $range)
    {
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask     = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);

                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            }
            // $netmask is a CIDR size block
            // fix the range argument
            $x = explode('.', $range);
            while (count($x) < 4) {
                $x[] = '0';
            }
            list($a, $b, $c, $d) = $x;
            $range               = sprintf('%u.%u.%u.%u', empty($a)?'0':$a, empty($b)?'0':$b, empty($c)?'0':$c, empty($d)?'0':$d);
            $range_dec           = ip2long($range);
            $ip_dec              = ip2long($ip);

            # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
            #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

            # Strategy 2 - Use math to create it
            $wildcard_dec = pow(2, (32 - $netmask)) - 1;
            $netmask_dec  = ~ $wildcard_dec;

            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
        }
        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (strpos($range, '*') !== false) { // a.b.*.* format
          // Just convert to A-B format by setting * to 0 for A and 255 for B
          $lower   = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }

        if (strpos($range, '-') !== false) { // A-B format
            list($lower, $upper) = explode('-', $range, 2);
            $lower_dec           = (float) sprintf('%u', ip2long($lower));
            $upper_dec           = (float) sprintf('%u', ip2long($upper));
            $ip_dec              = (float) sprintf('%u', ip2long($ip));

            return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
        }

        echo 'Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format';

        return false;
    }
}

if (!function_exists('getExpiredStaff')) {
    function getExpiredStaff()
    {
        $db = db_connect('default');
        $users = $db->table("users")->get()->getResultArray();
        $currentDate  = strtotime(date('Y-m-d'));

        return array_filter($users, function($value) use ($currentDate) {
            return $currentDate > strtotime($value['expiry_date'] ?? '') && !empty($value['expiry_date']);
        });
    }
}

function sendEmailRiseguard($template_name, $user_data, $ipLogData)
{
    $request = request();
    $parser = service('parser');
    $Email_templates_model = new Email_templates_model();
    //get the login details template
    $email_template = $Email_templates_model->get_final_template($template_name); //use default template
    
    $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
    $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
    $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
    $parser_data["RECIPIENTS_EMAIL_ADDRESS"] = $user_data["email"];
    $parser_data['ISP'] = $ipLogData['isp'];
    $parser_data['IP_ADDRESS'] = $ipLogData['ip'];
    $parser_data['COUNTRY'] = $ipLogData['country'];

    $message = $parser->setData($parser_data)->renderString($email_template->message);
    $subject = $parser->setData($parser_data)->renderString($email_template->subject);

    if (send_app_mail($request->getPost('email'), $subject, $message)) {
        return true;
    }
    return false;
}