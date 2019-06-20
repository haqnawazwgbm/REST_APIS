<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends General_Functions {

	 function __construct() {
        parent::__construct();

    }



    public function register() {
        if ($this->input->post('submitRegistration')) {
                
            
                $this->form_validation->set_rules('first_name', 'first_name', 'required');
                $this->form_validation->set_rules('last_name', 'last_name', 'required');
                $this->form_validation->set_rules('email', 'email', 'required|is_unique[t_users.email]');
                $this->form_validation->set_rules('password', 'password', 'required');
                $this->form_validation->set_rules('balance', 'balance', 'required');
                
                $account_no = $this->generate_account_no();

                $userData = array(
                    'account_no' => $account_no,
                    'first_name' => strip_tags($this->input->post('first_name')),
                    'last_name' => strip_tags($this->input->post('last_name')),
                    'email' => strip_tags($this->input->post('email')),
                    'password' => strip_tags($this->input->post('password')),
                    'balance' => strip_tags($this->input->post('balance')),
                    'role' => 'user',
                    'status' => 1,
                ); 

                if ($this->form_validation->run() == true) {
                    $id = $this->Common_Model->insert($userData, 't_users');
                    if($id){

                        $result = $this->user_unactive($userData);
                        if ($result) {
                            $response = array(
                                'status' => 1,
                                'message' => "Registration successfull. Please check your email for account activiations."
                            );
                            $this->output->set_content_type('application/json')->set_output(json_encode($response));
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => "Registration successfull but activation email not sent successfully."
                            );
                            $this->output->set_content_type('application/json')->set_output(json_encode($response));
                            return false;
                        }
                        
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => "Something went wrong. Please try again."
                        );
                        $this->output->set_content_type('application/json')->set_output(json_encode($response));
                        
                    }
                } else {
                    $errors = $this->form_validation->error_array();
 
                    $response = array(
                        'status' => 0,
                        'message' => $errors
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }

        } 
    }

    public function login() {
        
		if($this->input->post('login')){
            $this->form_validation->set_rules('password', 'password', 'required');
            if ($this->form_validation->run() == true) {
                $con['returnType'] = 'single';
                $email = strip_tags($this->input->post('email'));
                $account_no = strip_tags($this->input->post('account_no'));
                $password = strip_tags($this->input->post('password'));
              
                $con['string_conditions'] = array("email = '$email' and password = '$password' and status = 1 or account_no = $account_no and password = '$password' and status = 1");
                $checkLogin = $this->Common_Model->getRows($con, 't_users');
                if($checkLogin){
                    $con2['conditions'] = array(
                        'email' => $checkLogin['email'],
                        'token' => md5($checkLogin['email'])

                    );
                    $checkActivation = $this->Common_Model->getRows($con2, 't_user_activations');
                    if ($checkActivation) {
                        $response = array(
                            'status' => 0,
                            'message' => "Please check your email to activate your account then try login."
                        );
                        $this->output->set_content_type('application/json')->set_output(json_encode($response));
                        return false;
                    }
                    $this->session->set_userdata('isUserLoggedIn',TRUE);
                    $this->session->set_userdata('user',$checkLogin);
                    $user = $this->session->userdata('user');
                    $response = array(
                        'status' => 1,
                        'message' => "Login successfull."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => "Invalid login credentials. Please try again."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            } else {

            	$response = array(
                    'status' => 0,
                    'message' => "Invalid login credentials. Please try again."
                );
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
        }
	}

/*
     * User logout
     */
    public function logout(){
        $this->session->unset_userdata('isUserLoggedIn');
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        $response = array(
            'status' => 1,
            'message' => "Logout successfull."
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /* Activate user account
    */
    public function activate_account($token) {
        $con['returnType'] = 'single';
        $con['conditions'] = array(
            'token' => $token
        );
        $result = $this->Common_Model->getRows($con, 't_user_activations');
        if ($result) {

            $this->Common_Model->delete($con['conditions'], 't_user_activations');

            $response = array(
                'status' => 1,
                'message' => "Activated successfully. Please login."
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            $this->error();
        }
    }

    /* 
    *  keep user user unactive
    */
    public function user_unactive($data = array()) {
        if ($this->input->post('submitRegistration')) {
            $userData = array(
                'email' => $this->input->post('email'),
                'token' => md5($this->input->post('email'))
            );

            $this->Common_Model->insert($userData, 't_user_activations');

            $userData['message'] = 'Click the below link to activate your account. <br /><a href="' . base_url() . 'Login/activate_account/' . $userData['token'] . '">Activate</a><br>
                And here your account credentials<br>
                Account Number: '.$data['account_no'].'<br>
                Email: '.$data['email'].'<br>
                Password: '.$data['password'];
            $userData['subject'] = 'Account activation';

            $result = $this->sendMail($userData);
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

}