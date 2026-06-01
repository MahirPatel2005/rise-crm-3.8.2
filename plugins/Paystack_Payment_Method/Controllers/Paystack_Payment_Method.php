<?php

namespace Paystack_Payment_Method\Controllers;

use App\Controllers\Security_Controller;

class Paystack_Payment_Method extends Security_Controller {

    private $Paystack_ipn_model;
    private $paystack_config;

    function __construct() {
        parent::__construct(false);

        $this->Paystack_ipn_model = new \Paystack_Payment_Method\Models\Paystack_ipn_model();
        $this->paystack_config = $this->Payment_methods_model->get_oneline_payment_method("paystack");
    }

    function index() {
        show_404();
    }

    function initiate_payment_process() {
        $this->validate_submitted_data(array(
            "invoice_id" => "required|numeric",
            "currency" => "required",
            "balance_due" => "required",
            "payment_method_id" => "required",
        ));

        $invoice_id = $this->request->getPost("invoice_id");
        if (!$invoice_id) {
            show_404();
        }

        $login_user = isset($this->login_user->id) ? $this->login_user->id : 0;
        $payment_amount = $this->request->getPost("payment_amount");
        $verification_code = $this->request->getPost("verification_code");
        $contact_user_id = $login_user ? $login_user : $this->request->getPost("contact_user_id");
        if (!$contact_user_id) {
            show_404();
        }

        $currency = $this->request->getPost("currency");
        $balance_due = $this->request->getPost("balance_due");
        $client_id = $this->request->getPost("client_id");
        $payment_method_id = $this->request->getPost("payment_method_id");

        //validate public invoice information
        if (!$login_user && !validate_invoice_verification_code($verification_code, array("invoice_id" => $invoice_id, "client_id" => $client_id, "contact_id" => $contact_user_id))) {
            show_404();
        }

        //check if partial payment allowed or not
        if (get_setting("allow_partial_invoice_payment_from_clients")) {
            $payment_amount = unformat_currency($payment_amount);
        } else {
            $payment_amount = $balance_due;
        }

        //validate payment amount
        if ($payment_amount < $this->paystack_config->minimum_payment_amount * 1) {
            $error_message = app_lang('minimum_payment_validation_message') . " " . to_currency($this->paystack_config->minimum_payment_amount, $currency . " ");
            echo json_encode(array("success" => false, 'message' => $error_message));
            return false;
        }

        //we'll verify the transaction with a random string code after completing the transaction
        $payment_verification_code = make_random_string();
        $paystack_ipn_data = array(
            "verification_code" => $verification_code,
            "invoice_id" => $invoice_id,
            "contact_user_id" => $contact_user_id,
            "client_id" => $client_id,
            "payment_method_id" => $payment_method_id,
            "payment_verification_code" => $payment_verification_code
        );

        $contact_info = $this->Users_model->get_one($contact_user_id);
        $fields = [
            'email' => $contact_info->email,
            'amount' => ($payment_amount * 100), //Paystack will devide it with 100
            'callback_url' => get_uri("paystack_payment_method/redirect/$payment_verification_code"),
            'currency' => $currency,
            'metadata' => $paystack_ipn_data
        ];

        try {
            $authorization_result = $this->authorize_transaction($fields);
            $transaction_id = $this->verify_transaction_and_get_transaction_id($authorization_result->data->reference);
        } catch (\Exception $ex) {
            echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
            return false;
        }

        if ($transaction_id) {
            //so, the session creation is success
            //save ipn data to db
            $paystack_ipn_data["transaction_id"] = $transaction_id;
            $this->Paystack_ipn_model->ci_save($paystack_ipn_data);

            echo json_encode(array("success" => true, 'redirect_to' => $authorization_result->data->authorization_url));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            return false;
        }
    }

    private function verify_transaction_and_get_transaction_id($reference_id) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->paystack_config->secret_key,
                "Cache-Control: no-cache",
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $result = $this->common_error_handling_for_curl($result, $err);

        if (!$result->data->id) {
            //no transaction id
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            exit();
        }

        return $result->data->id;
    }

    private function authorize_transaction($fields) {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->paystack_config->secret_key,
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $result = $this->common_error_handling_for_curl($result, $err);

        if (!($result->data->reference && $result->data->authorization_url)) {
            //no reference id
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            exit();
        }

        return $result;
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

        if (!$result) {
            //got bad result
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            exit();
        }

        if (!$result->status) {
            //got error message from curl
            echo json_encode(array("success" => false, 'message' => $result->message));
            exit();
        }

        return $result;
    }

    private function get_payment_info($transaction_id) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/$transaction_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->paystack_config->secret_key,
                "Cache-Control: no-cache",
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return $this->common_error_handling_for_curl($result, $err);
    }

    function redirect($payment_verification_code = "") {
        if (!$payment_verification_code) {
            show_404();
        }

        $paystack_ipn_info = $this->Paystack_ipn_model->get_one_payment_where($payment_verification_code);
        if (!$paystack_ipn_info) {
            show_404();
        }

        $response = $this->get_payment_info($paystack_ipn_info->transaction_id);

        //check if the payment is valid
        if ($response->data->status !== "success") {
            show_404();
        }

        //check if there has any payment with this transaction id
        $invoice_payment_options = array("transaction_id" => $paystack_ipn_info->transaction_id);
        $invoice_payment_info = $this->Invoice_payments_model->get_one_where($invoice_payment_options);
        if ($invoice_payment_info->id) {
            show_404();
        }

        $now = get_current_utc_time();
        $invoice_id = $paystack_ipn_info->invoice_id;
        $contact_user_id = $paystack_ipn_info->contact_user_id;

        $invoice_payment_data = array(
            "invoice_id" => $invoice_id,
            "payment_date" => $now,
            "payment_method_id" => $paystack_ipn_info->payment_method_id,
            "note" => "",
            "amount" => (($response->data->amount * 1) / 100),
            "transaction_id" => $paystack_ipn_info->transaction_id,
            "created_at" => $now,
            "created_by" => $contact_user_id,
        );

        $invoice_payment_id = $this->Invoice_payments_model->ci_save($invoice_payment_data);
        if (!$invoice_payment_id) {
            show_404();
        }

        //as receiving payment for the invoice, we'll remove the 'draft' status from the invoice 
        $this->Invoices_model->update_invoice_status($invoice_id);

        log_notification("invoice_payment_confirmation", array("invoice_payment_id" => $invoice_payment_id, "invoice_id" => $invoice_id), "0");
        log_notification("invoice_online_payment_received", array("invoice_payment_id" => $invoice_payment_id, "invoice_id" => $invoice_id), $contact_user_id);

        //delete the ipn data
        $this->Paystack_ipn_model->delete_permanently($paystack_ipn_info->id);

        $verification_code = $paystack_ipn_info->verification_code;
        if ($verification_code) {
            $redirect_to = "pay_invoice/index/$verification_code";
        } else {
            $redirect_to = "invoices/preview/$invoice_id";
        }

        $this->session->setFlashdata("success_message", app_lang("payment_success_message"));
        app_redirect($redirect_to);
    }

}
