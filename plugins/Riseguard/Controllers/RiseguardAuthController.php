<?php

namespace Riseguard\Controllers;

use App\Controllers\App_Controller;

class RiseguardAuthController extends App_Controller
{
    protected $riseguardmodel;
    protected $settings;
    protected $signin_validation_errors;

    function __construct()
    {
        parent::__construct();

        $this->riseguardmodel = model('Riseguard\Models\RiseguardModel');
        $this->settings = new \App\Models\Settings_model();
        $this->signin_validation_errors = [];

        helper('email');
    }

    public function resetSession()
    {
        if ($this->request->getPost()) {
            $validation = $this->validate_submitted_data([
                'email'    => 'required|valid_email',
                'password' => 'required'
            ], true);

            $email = $this->request->getPost("email");
            $password = $this->request->getPost("password");
            if (!$email) {
                app_redirect('riseguard/reset_session');
            }

            if (is_array($validation)) {
                //has validation errors
                $this->signin_validation_errors = $validation;
            }

            //don't check password if there is any error
            if ($this->signin_validation_errors) {
                $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
                app_redirect('riseguard/reset_session');
            }

            if (!$this->Users_model->authenticate($email, $password)) {
                //authentication failed
                array_push($this->signin_validation_errors, app_lang("authentication_failed"));
                $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
                app_redirect('riseguard/reset_session');
            }

            $this->Users_model->sign_out();
        }

        $viewData['title'] = app_lang('reset_session');
        
        return $this->template->view('Riseguard\Views\reset_session',$viewData);
    }

}
