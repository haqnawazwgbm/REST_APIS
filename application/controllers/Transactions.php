<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends Check_Logged {
    private $user;

     function __construct() {
        parent::__construct();
        $this->user = $this->session->userdata('user');
    }

     // Get loged in user info
    public function transactions()
    {

            $con['conditions'] = array(
                'user_id' => $this->user['id']
            );
            $transactions = $this->Common_Model->getRows($con, 't_user_transactions');

            $this->output->set_content_type('application/json')->set_output(json_encode($transactions));  
    }

       public function transaction() {
                $this->form_validation->set_rules('first_name', 'first_name', 'required');
                $this->form_validation->set_rules('last_name', 'last_name', 'required');
                $this->form_validation->set_rules('phone_no', 'phone_no', 'required');
                $this->form_validation->set_rules('email', 'email', 'required');
                $this->form_validation->set_rules('amount', 'amount', 'required');
                $this->form_validation->set_rules('secret_question', 'secret_question', 'required');

                // Check balance
                
                if (! $this->check_balance()) {
                    $response = array(
                        'status' => 0,
                        'message' => "Transaction faild. Your current balance are less than from your transacted amount."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));  
                    return false;
                }

                // Check the account upgrades.
                $con['conditions'] = array(
                    'user_id' => $this->user['id']
                );
                $con['selection'] = 'sum(t_user_transactions.amount) as total_amount, t_user_transactions.*';
                //$con['groupBy'] = array("t_user_transactions.user_id");
                $con['returnType'] = 'single';
                $transactions = $this->Common_Model->getRows($con, 't_user_transactions');
                if ($transactions['total_amount'] > 250 && $transactions['proof_of_address'] == '0' && $transactions['proof_of_id'] == '0') {
                    $response = array(
                        'status' => 0,
                        'message' => "Your transactions amount are grater than 250$. Please upgrade your account."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response)); 
                    return false;
                } elseif ($transactions['total_amount'] > 1000 && $transactions['proof_of_address'] != '0' && $transactions['proof_of_id'] == '0') {
                    $response = array(
                        'status' => 0,
                        'message' => "Your transactions amount are grater than 1000$. Please upgrade your account."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return false;
                } elseif ($transactions['total_amount'] > 5000 && $transactions['proof_of_address'] != '0' && $transactions['proof_of_id'] != '0') {
                    $response = array(
                        'status' => 0,
                        'message' => "Your transactions amount are grater than 5000$. Please upgrade your account."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return false;
                }

                // Calculate the transaction commission.
                $amount = strip_tags($this->input->post('amount'));
                $per = $amount / 100;
                $commission = $per * 5;

                $actual_amount = $amount - $commission;

                $userData = array(
                    'transaction_no' => $this->getGUIDnoHash(),
                    'first_name' => strip_tags($this->input->post('first_name')),
                    'last_name' => strip_tags($this->input->post('last_name')),
                    'phone_no' => strip_tags($this->input->post('phone_no')),
                    'email' => strip_tags($this->input->post('email')),
                    'amount' => $actual_amount,
                    'secret_question' => strip_tags($this->input->post('secret_question')),
                    'cashout' => 0,
                    'user_id' => $this->user['id']
                    
                ); 

                if ($this->form_validation->run() == true) {
                    $this->Common_Model->insert($userData, 't_user_transactions');
                    $userData2 = array(
                        'balance' => "balance + $commission"
                    );
                    $condition = array('id' => 1);
                    $update = $this->Common_Model->update_fields($userData2, $condition, 't_users');

                    $userData2 = array(
                        'balance' => "balance - $amount"
                    );
                    $condition = array('id' => $this->user['id']);
                    $update = $this->Common_Model->update_fields($userData2, $condition, 't_users');

                    extract($userData);
                    $mailPara['message'] = "Below are the transaction details. <br />
                                            Transaction Number: $transaction_no<br />
                                            First Name: $first_name<br />
                                            Last Name: $last_name<br />
                                            Phone Number: $phone_no<br />
                                            Email: $email<br />
                                            Amount: $actual_amount<br />
                                            Secret Question: $secret_question<br />";
                    $mailPara['subject'] = 'Transaction Details.';
                    $mailPara['email'] = $email;

                    $result = $this->sendMail($mailPara);
                    $response = array(
                        'status' => 1,
                        'message' => "Transaction created successfully."
                    );
                     $this->output->set_content_type('application/json')->set_output(json_encode($response));               
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => "Something went wront. Please try again."
                    );
                     $this->output->set_content_type('application/json')->set_output(json_encode($response)); 
                     return false;    
                }
    }


    function getGUIDnoHash(){
            mt_srand((double)microtime()*10000);
            $charid = md5(uniqid(rand(), true));
            $c = unpack("C*",$charid);
            $c = implode("",$c);

            return substr($c,0,20);
    }

    function check_balance() {
        $amount = $this->input->post('amount');
        $con['conditions'] = array(
            'id' => $this->user['id']
        );
        $con['returnType'] = 'single';
        $balance = $this->Common_Model->getRows($con, 't_users');
        if ($balance['balance'] < $amount) {
            return false;

        } else {
            return true;
        }
    }

}