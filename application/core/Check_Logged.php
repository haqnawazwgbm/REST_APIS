<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Check_Logged extends General_Functions {


	public function __construct()
    {
        parent::__construct();
        if( ! $this->session->userdata('isUserLoggedIn')) {
            redirect('Login/login_form');
        } else {
        	$user = $this->session->userdata('user');
        }        
    }

    

}

