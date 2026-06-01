<?php

namespace PayU_Payment_Method\Controllers;

use App\Controllers\Security_Controller;

class PayU_Payment_Method extends Security_Controller {

    private $PayU_ipn_model;

    function __construct() {
        parent::__construct(false);

        //load resource
        require_once(PLUGINPATH . "PayU_Payment_Method/ThirdParty/openpayu_php/vendor/autoload.php");
        $this->PayU_ipn_model = new \PayU_Payment_Method\Models\PayU_ipn_model();
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
        $description = $this->request->getPost("description");

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

        $payu_config = $this->Payment_methods_model->get_oneline_payment_method("payu");

        //validate payment amount
        if ($payment_amount < $payu_config->minimum_payment_amount * 1) {
            $error_message = app_lang('minimum_payment_validation_message') . " " . to_currency($payu_config->minimum_payment_amount, $currency . " ");
            echo json_encode(array("success" => false, 'message' => $error_message));
            return false;
        }

        $this->create_payu_configuration();

        //we'll verify the transaction with a random string code after completing the transaction
        $payment_verification_code = make_random_string();

        $payu_ipn_data = array(
            "verification_code" => $verification_code,
            "invoice_id" => $invoice_id,
            "contact_user_id" => $contact_user_id,
            "client_id" => $client_id,
            "payment_method_id" => $payment_method_id,
            "payment_verification_code" => $payment_verification_code
        );

        //create payment
        $payment_data['continueUrl'] = get_uri("payu_payment_method/redirect/$payment_verification_code");
        $payment_data['customerIp'] = $_SERVER['REMOTE_ADDR'];
        $payment_data['merchantPosId'] = \OpenPayU_Configuration::getMerchantPosId();

        $payment_data['description'] = $description;
        $payment_data['visibleDescription'] = $description;
        $payment_data['currencyCode'] = $currency;
        $payment_data['totalAmount'] = $payment_amount * 100; //PayU will devide it with 100

        $payment_data['products'][0]['name'] = $description;
        $payment_data['products'][0]['unitPrice'] = $payment_amount;
        $payment_data['products'][0]['quantity'] = 1;

        $contact_info = $this->Users_model->get_one($contact_user_id);
        $payment_data['buyer']['email'] = $contact_info->email;
        $payment_data['buyer']['phone'] = $contact_info->phone;
        $payment_data['buyer']['firstName'] = $contact_info->first_name;
        $payment_data['buyer']['lastName'] = $contact_info->last_name;

        try {
            $response = \OpenPayU_Order::create($payment_data);

            if ($response->getResponse()->orderId && $response->getResponse()->redirectUri) {
                //so, the session creation is success
                //save ipn data to db
                $payu_ipn_data["order_id"] = $response->getResponse()->orderId;
                $this->PayU_ipn_model->ci_save($payu_ipn_data);

                echo json_encode(array("success" => true, 'redirect_to' => $response->getResponse()->redirectUri));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
                return false;
            }
        } catch (\Exception $ex) {
            echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
            return false;
        }
    }

    private function create_payu_configuration() {
        $payu_config = $this->Payment_methods_model->get_oneline_payment_method("payu");

        try {
            //set Sandbox Environment
            if ($payu_config->payu_testing_environment) {
                \OpenPayU_Configuration::setEnvironment('sandbox');
            } else {
                \OpenPayU_Configuration::setEnvironment('secure');
            }

            \OpenPayU_Configuration::setMerchantPosId($payu_config->pos_id);
            \OpenPayU_Configuration::setSignatureKey($payu_config->second_key);

            \OpenPayU_Configuration::setOauthClientId($payu_config->oauth_client_id);
            \OpenPayU_Configuration::setOauthClientSecret($payu_config->oauth_client_secret);
        } catch (\Exception $ex) {
            echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
            exit;
        }
    }

    function redirect($payment_verification_code = "") {
        if (!$payment_verification_code) {
            show_404();
        }

        $payu_ipn_info = $this->PayU_ipn_model->get_one_payment_where($payment_verification_code);
        if (!$payu_ipn_info) {
            show_404();
        }

        $this->create_payu_configuration();

        try {
            $response = \OpenPayU_Order::retrieve($payu_ipn_info->order_id);
        } catch (\Exception $ex) {
            echo json_encode(array("success" => false, 'message' => $ex->getMessage()));
            exit;
        }

        //check if the payment is valid
        if (!($response->getStatus() === 'SUCCESS' && $response->getResponse()->orders[0]->status === "COMPLETED")) {
            show_404();
        }

        //check if there has any payment with this transaction id
        $invoice_payment_options = array("transaction_id" => $payu_ipn_info->order_id);
        $invoice_payment_info = $this->Invoice_payments_model->get_one_where($invoice_payment_options);
        if ($invoice_payment_info->id) {
            show_404();
        }

        $now = get_current_utc_time();
        $invoice_id = $payu_ipn_info->invoice_id;
        $contact_user_id = $payu_ipn_info->contact_user_id;

        $invoice_payment_data = array(
            "invoice_id" => $invoice_id,
            "payment_date" => $now,
            "payment_method_id" => $payu_ipn_info->payment_method_id,
            "note" => "",
            "amount" => (($response->getResponse()->orders[0]->totalAmount * 1) / 100),
            "transaction_id" => $payu_ipn_info->order_id,
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
        $this->PayU_ipn_model->delete_permanently($payu_ipn_info->id);

        $verification_code = $payu_ipn_info->verification_code;
        if ($verification_code) {
            $redirect_to = "pay_invoice/index/$verification_code";
        } else {
            $redirect_to = "invoices/preview/$invoice_id";
        }

        $this->session->setFlashdata("success_message", app_lang("payment_success_message"));
        app_redirect($redirect_to);
    }

}