<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Check_User_Logged extends General_Functions {


	public function __construct()
    {
        parent::__construct();
        if( ! $this->session->userdata('isUserLoggedIn')) {
            redirect('Site_Home/');
        }        
    }

 

}

