<?php

namespace Skype_Integration\Libraries;

class Skype_Integration {

    private $responseCode = 0;

    public function __construct() {
        $this->client_id = get_skype_integration_setting('client_id');
        $this->client_secret = get_skype_integration_setting('client_secret');
        $this->login_url = "https://login.microsoftonline.com/common/oauth2/v2.0";
        $this->graph_url = "https://graph.microsoft.com/beta/me/events";
        $this->redirect_uri = get_uri("skype_integration_settings/save_access_token");
    }

    //authorize connection
    public function authorize() {
        $url = "$this->login_url/authorize?";
        $auth_array = array(
            "client_id" => $this->client_id,
            "response_type" => "code",
            "redirect_uri" => $this->redirect_uri,
            "response_mode" => "query",
            "scope" => "offline_access%20user.read%20Calendars.ReadWrite",
        );

        foreach ($auth_array as $key => $value) {
            $url .= "$key=$value";

            if ($key !== "scope") {
                $url .= "&";
            }
        }

        app_redirect($url, true);
    }

    private function common_error_handling_for_curl($result, $err) {
        try {
            $result = json_decode($result);
        } catch (\Exception $ex) {
            echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
            exit();
        }

        if ($err) {
            //got curl error
            echo json_encode(array("success" => false, 'message' => "cURL Error #:" . $err));
            exit();
        }

        if (isset($result->error_description) && $result->error_description) {
            //got error message from curl
            echo json_encode(array("success" => false, 'message' => $result->error_description));
            exit();
        }

        if (isset($result->error) && $result->error &&
                isset($result->error->message) && $result->error->message &&
                isset($result->error->code) && $result->error->code !== "InvalidAuthenticationToken") {
            //got error message from curl
            echo json_encode(array("success" => false, 'message' => $result->error->message));
            exit();
        }

        return $result;
    }

    //fetch access token with auth code and save to database
    public function save_access_token($code, $is_refresh_token = false) {
        $fields = array(
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "redirect_uri" => $this->redirect_uri,
            "scope" => "Calendars.ReadWrite",
            "grant_type" => "authorization_code",
        );

        if ($is_refresh_token) {
            $fields["refresh_token"] = $code;
            $fields["grant_type"] = "refresh_token";
        } else {
            $fields["code"] = $code;
        }

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, "$this->login_url/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cache-Control: no-cache",
            "Content-Type: application/x-www-form-urlencoded",
        ));

        //So that curl_exec returns the contents of the cURL;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $result = $this->common_error_handling_for_curl($result, $err);

        if (!((!$is_refresh_token && isset($result->access_token) && isset($result->refresh_token)) || ($is_refresh_token && isset($result->access_token)))) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            exit();
        }

        if ($is_refresh_token) {
            //while refreshing token, refresh_token value won't be available
            $result->refresh_token = $code;
        }

        // Save the token to database
        $new_access_token = json_encode($result);

        if ($new_access_token) {
            $Skype_Integration_settings_model = new \Skype_Integration\Models\Skype_Integration_settings_model();
            $Skype_Integration_settings_model->save_setting('oauth_access_token', $new_access_token);

            //got the valid access token. store to setting that it's authorized
            $Skype_Integration_settings_model->save_setting('skype_authorized', "1");
        }
    }

    //add/update the event of api
    public function save_event($data = array(), $id = 0) {
        if (!$data) {
            return false;
        }

        //prepare data
        $calendar_event_info = new \stdClass();

        $start_time = get_array_value($data, "start_time");
        $in_time = is_date_exists($start_time) ? convert_date_utc_to_local($start_time) : "";
        if (get_setting("time_format") == "24_hours") {
            $in_time_value = $in_time ? date("H:i", strtotime($in_time)) : "";
        } else {
            $in_time_value = $in_time ? convert_time_to_12hours_format(date("H:i:s", strtotime($in_time))) : "";
        }

        $start_date = $in_time ? date("Y-m-d", strtotime($in_time)) : "";
        $start_time = $in_time_value;

        $date_time = new \DateTime($start_date . " " . $start_time, new \DateTimeZone(get_setting("timezone")));
        $start_time = $date_time->format(\DateTime::RFC3339);

        $event = array(
            "subject" => get_array_value($data, "title"),
            "body" => array(
                "contentType" => "HTML",
                "content" => get_array_value($data, "description")
            ),
            "start" => array(
                "dateTime" => $start_time,
                "timeZone" => get_setting("timezone")
            ),
            "end" => array(
                "dateTime" => $start_time,
                "timeZone" => get_setting("timezone")
            ),
            "isOnlineMeeting" => true,
            "onlineMeetingProvider" => "skypeForConsumer",
        );

        $Skype_meetings_model = new \Skype_Integration\Models\Skype_meetings_model();
        $event_info = $Skype_meetings_model->get_one($id);
        if ($event_info->event_id) {
            //update operation
            $calendar_event_info = $this->do_request("PATCH", "/$event_info->event_id", $event);
        } else if (!$event_info->event_id) {
            //insert operation
            $calendar_event_info = $this->do_request("POST", "", $event);
        }

        //save newly added event information
        if (isset($calendar_event_info->id) && isset($calendar_event_info->onlineMeeting->joinUrl)) {
            $data = array(
                "event_id" => $calendar_event_info->id,
                "join_url" => $calendar_event_info->onlineMeeting->joinUrl
            );

            return $data;
        }
    }

    private function headers($access_token) {
        return array(
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        );
    }

    private function do_request($method, $path, $body = array()) {
        if (is_array($body)) {
            // Treat an empty array in the body data as if no body data was set
            if (!count($body)) {
                $body = '';
            } else {
                $body = json_encode($body);
            }
        }

        $oauth_access_token = get_skype_integration_setting("oauth_access_token");
        $oauth_access_token = json_decode($oauth_access_token);

        $method = strtoupper($method);
        $url = $this->graph_url . $path;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers($oauth_access_token->access_token));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (in_array($method, array('DELETE', 'PATCH', 'POST', 'PUT', 'GET'))) {

            // All except DELETE can have a payload in the body
            if ($method != 'DELETE' && strlen($body)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = $this->common_error_handling_for_curl($result, $err);

        if (isset($result->error->code) && $result->error->code === "InvalidAuthenticationToken") {
            //access token is expired
            $this->save_access_token($oauth_access_token->refresh_token, true);
            return $this->do_request($method, $path, $body);
        }

        return $result;
    }

    //delete event
    public function delete($event_id = "") {
        if (!$event_id) {
            return false;
        }

        $this->do_request("DELETE", "/$event_id");
        if ($this->responseCode === 204) {
            return true;
        }
    }

}
