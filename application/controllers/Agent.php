<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends Check_Logged {
    private $user;

     function __construct() {
        parent::__construct();
        $this->user = $this->session->userdata('user');
    }

    public function get_today_cashout() {
        $con['conditions'] = array(
            't_fee_transactions.user_id' => $this->user['id'],
            't_fee_transactions.created' => date('Y-m-d')
        );
        $con['innerJoin'] = array(array(
            'table' => 't_user_transactions',
            'condition' =>'t_user_transactions.id = t_fee_transactions.user_transaction_id',
            'joinType' => 'inner'
        ));
        $cashouts = $this->Common_Model->getRows($con, 't_fee_transactions');

        $this->output->set_content_type('application/json')->set_output(json_encode($cashouts)); 

    }

    public function get_all_clients() {
        $con['conditions'] = array(
            'role !=' => 'admin',
            'status' => 1 
        );
        $users = $this->Common_Model->getRows($con, 't_users');
        $this->output->set_content_type('application/json')->set_output(json_encode($users)); 
    }

    public function clients_balance() {
        $con['selection'] = 't_users.first_name, t_users.last_name, t_users.email, t_users.account_no, t_users.balance';
        $con['conditions'] = array(
            'role !=' => 'admin',
            'status' => 1
        );
        $balances = $this->Common_Model->getRows($con, 't_users');
        $this->output->set_content_type('application/json')->set_output(json_encode($balances)); 
    }

    public function clients_transactions() {
        $con['selection'] = 't_user_transactions.amount, t_user_transactions.cashout, t_user_transactions.created as date, t_users.first_name, t_users.last_name, t_users.email';
        $con['conditions'] = array(
            't_users.role !=' => 'admin',
            't_users.status' => 1
        );
        $con['innerJoin'] = array(array(
            'table' => 't_user_transactions',
            'condition' =>'t_user_transactions.user_id = t_users.id',
            'joinType' => 'inner'
        ));
        $balances = $this->Common_Model->getRows($con, 't_users');
        $this->output->set_content_type('application/json')->set_output(json_encode($balances)); 
    }

    public function get_today_balance() {
        $con['selection'] = "t_users.day_amount as remaining_balance";
        $con['conditions'] = array(
            'id' => $this->user['id']
        );
        $con['returnType'] = 'single';
        $balance = $this->Common_Model->getRows($con, 't_users');

        $this->output->set_content_type('application/json')->set_output(json_encode($balance));
    }

   
           public function cashout() {
                $this->form_validation->set_rules('transaction_no', 'transaction_no', 'required');
                $this->form_validation->set_rules('first_name', 'first_name', 'required');
                $this->form_validation->set_rules('last_name', 'last_name', 'required');
                $this->form_validation->set_rules('phone_no', 'phone_no', 'required');
                $this->form_validation->set_rules('email', 'email', 'required');
                $this->form_validation->set_rules('amount', 'amount', 'required');
                $this->form_validation->set_rules('secret_question', 'secret_question', 'required');

                $con['conditions'] = array(
                    'transaction_no' => strip_tags($this->input->post('transaction_no')),
                    'first_name' => strip_tags($this->input->post('first_name')),
                    'last_name' => strip_tags($this->input->post('last_name')),
                    'phone_no' => strip_tags($this->input->post('phone_no')),
                    'email' => strip_tags($this->input->post('email')),
                    'amount' => strip_tags($this->input->post('amount')),
                    'secret_question' => strip_tags($this->input->post('secret_question')),
                );
                $con['returnType'] = 'single';
                $transaction = $this->Common_Model->getRows($con, 't_user_transactions');

                // increment 5% in amount to become old amount.
                $amount = $transaction['amount'];
                $per = $amount / 100;
                $commission = $per * 5;
                $transfer_amount = $amount + $commission;

                // The current day amount for agent.
                $con['conditions'] = array(
                    'id' => $this->user['id']
                );
                $con['returnType'] = 'single';
                $user = $this->Common_Model->getRows($con, 't_users');
                if ($user['day_amount'] < $transfer_amount) {
                    // Send email for current day amount.
                    $mailPara['message'] = "Checkout failed. The transfered amount are $transfer_amount and your today amount are ".$user['day_amount'].".";
                    $mailPara['subject'] = 'Transaction Details.';
                    $mailPara['email'] = $this->user['email'];

                    $response = array(
                        'status' => 0,
                        'message' => "Checkout failed. The transfered amount are $transfer_amount and your today amount are ".$user['day_amount']."."
                    );
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return false;
                }

                // Decrement 1.5% amount from total transacted amount.
                $amount = $amount + $commission;
                $per = $amount / 100;
                $commission = $per * 5;

                $actual_amount = $amount - $commission;


                $userData = array(
                    'user_id' => $this->user['id'],
                    'user_transaction_id' => $transaction['id'],
                    'fee' => $commission,
                    'created' => date('Y-m-d')
                    
                ); 

                if ($this->form_validation->run() == true) {
                    $this->Common_Model->insert($userData, 't_fee_transactions');
                    $userData = array(
                        'balance' => "balance + $commission",
                        'day_amount' => "day_amount - $transfer_amount"
                    );
                    $condition = array('id' => $this->user['id']);
                    $update = $this->Common_Model->update_fields($userData, $condition, 't_users');

                    $userData = array(
                        'cashout' => 1
                    );
                    $condition = array('id' => $transaction['id']);
                    $udpate = $this->Common_Model->update($userData, $condition, 't_user_transactions');

                    $response = array(
                        'status' => 1,
                        'message' => "Cashout done successfully."
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

    public function current_day() {
        $amount = $this->input->post('amount');
        $userData = array(
            'day_amount' => "day_amount + $amount"
        );
        $condition = array('id' => $this->user['id']);
        $update = $this->Common_Model->update_fields($userData, $condition, 't_users');
        $response = array(
            'status' => 1,
            'message' => "Record updated successfully."
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($response)); 

    }


}