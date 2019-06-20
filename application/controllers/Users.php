<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Check_Logged {
    private $user;

     function __construct() {
        parent::__construct();
        $this->user = $this->session->userdata('user');
    }

     // Get all users
    public function index()
    {
            if ($this->user['role'] == 'admin') {
                $con['conditions'] = array();
            } else {
                $con['conditions'] = array(
                    'user_id' => $this->user['id'],
                );
            }
            
            $users = $this->Common_Model->getRows($con, 't_users');
            $users = $users ? $users : array();
            $this->output->set_content_type('application/json')->set_output(json_encode($users));  
    }


    // Get loged in user info
    public function get_info()
    {

            $con['conditions'] = array(
                'id' => $this->user['id']
            );
            $con['returnType'] = 'single';
            $user = $this->Common_Model->getRows($con, 't_users');

            $this->output->set_content_type('application/json')->set_output(json_encode($user));  
    }

       public function store() {
                $this->form_validation->set_rules('first_name', 'first_name', 'required');
                $this->form_validation->set_rules('last_name', 'last_name', 'required');
                $this->form_validation->set_rules('password', 'password', 'required');
                $this->form_validation->set_rules('email', 'email', 'required');
                $this->form_validation->set_rules('balance', 'balance', 'required');
                $this->form_validation->set_rules('role', 'role', 'required');
                $this->form_validation->set_rules('currency', 'currency', 'required');

                $userData = array(
                    'account_no' => $this->generate_account_no(),
                    'first_name' => strip_tags($this->input->post('first_name')),
                    'last_name' => strip_tags($this->input->post('last_name')),
                    'email' => strip_tags($this->input->post('email')),
                    'password' => strip_tags($this->input->post('password')),
                    'iban' => strip_tags($this->input->post('iban')),
                    'proof_of_id' => strip_tags($this->input->post('proof_of_id')),
                    'proof_of_address' => strip_tags($this->input->post('proof_of_address')),
                    'balance' => strip_tags($this->input->post('balance')),
                    'currency' => strip_tags($this->input->post('currency')),
                    'user_id' => $this->user['id'],
                    'role' => strip_tags($this->input->post('role')),
                    'status' => 1,
                ); 

                if ($this->form_validation->run() == true) {
                    $this->Common_Model->insert($userData, 't_users');
                    $response = array(
                        'status' => 1,
                        'message' => "Record created successfully."
                    );
                     $this->output->set_content_type('application/json')->set_output(json_encode($response));               
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => "Something went wront. Please try again."
                    );
                     $this->output->set_content_type('application/json')->set_output(json_encode($response));     
                }
    }



    public function edit() {
        

                $id = $this->input->input_stream('id');
                $userData = array(
                    'first_name' => strip_tags($this->input->input_stream('first_name')),
                    'last_name' => strip_tags($this->input->input_stream('last_name')),
                    'email' => strip_tags($this->input->input_stream('email')),
                    'password' => strip_tags($this->input->input_stream('password')),
                    'iban' => strip_tags($this->input->input_stream('iban')),
                    'balance' => strip_tags($this->input->input_stream('balance')),
                    'currency' => strip_tags($this->input->input_stream('currency')),
                    'proof_of_id' => strip_tags($this->input->input_stream('proof_of_id')),
                    'proof_of_address' => strip_tags($this->input->input_stream('proof_of_address')),
                    'role' => strip_tags($this->input->input_stream('role'))
                ); 

                 

                 $this->form_validation->set_data($userData);

                $this->form_validation->set_rules('first_name', 'first_name', 'required');
                $this->form_validation->set_rules('last_name', 'last_name', 'required');
                $this->form_validation->set_rules('password', 'password', 'required');
                $this->form_validation->set_rules('email', 'email', 'required');
                $this->form_validation->set_rules('balance', 'balance', 'required');
                $this->form_validation->set_rules('role', 'role', 'required');
                $this->form_validation->set_rules('currency', 'currency', 'required');

                $condition = array('id' => $id);
                 if ($this->form_validation->run() == true) {

                    $update = $this->Common_Model->update($userData, $condition, 't_users');
                    if($update){
                        $response = array(
                            'status' => 1,
                            'message' => "Record updated successfully."
                        );
                         $this->output->set_content_type('application/json')->set_output(json_encode($response)); 
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => "Something went wrong. Please try again."
                        );
                         $this->output->set_content_type('application/json')->set_output(json_encode($response)); 
                    }
                } else {
                        $response = array(
                            'status' => 0,
                            'message' => "Something went wrong. Please try again."
                        );
                        $this->output->set_content_type('application/json')->set_output(json_encode($response)); 
                }
    }

    public function delete() {
        $id = $this->input->input_stream('id');
        $condition = array('id' => $id);
        $this->Common_Model->delete($condition, 't_users');
        $response = array(
            'status' => 1,
            'message' => "Record deleted successfully."
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }


    public function update_password() {
        $old_password = $this->input->post('old_password');
        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');
        if ($password != $confirm_password) {
            $response = array(
                'status' => 0,
                'message' => "Password doesn't match."
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return false;
        }
        $con['conditions'] = array(
            'id' => $this->user['id'],
            'password' => $old_password
        );
        $user = $this->Common_Model->getRows($con, 't_users');

        if ($user) {
            $condition = array('id' => $this->user['id']);
            $userData = array(
                'password' => $password
            );
            $this->Common_Model->update($userData, $condition, 't_users');
            $response = array(
                'status' => 0,
                'message' => "Password updated successfully."
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            $response = array(
                'status' => 0,
                'message' => "Invalid old password."
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }
}