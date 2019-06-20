<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General_Functions extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->helper("file");
        $this->load->library('form_validation');
        $this->load->model('Common_Model');
        $this->load->helper('security');    
        $this->load->library('pagination');
        $this->lang->load('message','english');
        $this->load->helper('htmlpurifier');

        $header = $this->input->request_headers();

        $api_key = 'dgk2342k5664yhn54564chhg';
        $auth = @$header['Authorization'];
        $auth = explode(':', $auth);

        $head = explode(' ', @$auth[0]);
        $get_api_key = @$head[1];
        $time = @$auth[1];

        $get_signature = @$auth[2];
        $string = $api_key.':'.$time;

        
        $signature = hash_hmac('sha256', $string, $api_key, false);

        // check timestamp
        $timestamp = $this->check_timestamp($time);
        if ($timestamp) {
            $response = array(
                    'status' => 104,
                    'message' => "Timestamp already in use."
                );
            $this->output->set_content_type('application/json');
            echo(json_encode($response));
            exit;
        }  

        if ($api_key != $get_api_key) {
            $response = array(
                    'status' => 101,
                    'message' => "Invalid api key"
                );
            $this->output->set_content_type('application/json');
            echo(json_encode($response));
            exit;
        } elseif ($signature != $get_signature) {
            $response = array(
                    'status' => 102,
                    'message' => "Invalid signature"
                );
            $this->output->set_content_type('application/json');
            echo(json_encode($response));
            exit;
        }

        $userData = array(
            'timestamp' => $time
        );
        $this->Common_Model->insert($userData, 't_timestamp');

    }


    public function remaining_date($date) {
        $date = strtotime(date('Y-m-d H:i:s')) - strtotime($date);
        return date('i:s', $date);
    }





    public function error() {
        $response = array(
            'status' => 0,
            'message' => "Something went wrong. Please try again."
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

 



    function sendMail($userData)
    {
        $this->load->library('encrypt');
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_port'] = '465';
        $config['smtp_timeout'] = '30';
        $config['smtp_user'] = 'mail.2goud1@gmail.com';
        $config['smtp_pass'] = 'Initial123';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;
        $config['newline'] = "\r\n";

        
        $this->load->library('email', $config);
        $this->email->from('glogger@gmail.com'); // change it to yours
        $this->email->to($userData['email']);// change it to yours
        $this->email->subject($userData['subject']);
        $this->email->message($userData['message']);
        if($this->email->send())
        {
            return true;
        }
        else
        {
            return false;
        }

    }

      public function upload_single_file()
        {
                $config['upload_path']          = dirname($_SERVER["SCRIPT_FILENAME"])."/uploads/";
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
                $config['max_size']             = 2000;
                $config['max_width']            = 2024;
                $config['max_height']           = 2068;
                $config['encrypt_name']         = TRUE;

                $this->load->library('upload', $config);

                    if (!empty($_FILES['userfile']['name']))
                    {

                        if (!$this->upload->do_upload())
                        {
                            
                            exit('Only jpg, png and gif formats are supported.');

                        } else {
                            return $this->upload->data('file_name');
                        }
                    }  else {
                        return false;
                    }


            }

        /*
        * upload multiple files.
        */
        public function upload_multiple_files()
        {
                $config['upload_path']          = dirname($_SERVER["SCRIPT_FILENAME"])."/uploads/";
                $config['allowed_types']        = '*';
                $config['max_size']             = 4000;
                $config['max_width']            = 2024;
                $config['max_height']           = 2068;
                $config['encrypt_name']         = TRUE;
                $uploaded_path = array();

                $this->load->library('upload', $config);

                $files = $_FILES;
                $ctf = count($_FILES['userfile']['name']);
                  for($i=0; $i < $ctf; $i++)  //fieldname 
                {

                    $_FILES['userfile']['name']= $files['userfile']['name'][$i];
                    $_FILES['userfile']['type']= $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error']= $files['userfile']['error'][$i];
                    $_FILES['userfile']['size']= $files['userfile']['size'][$i];

                    if (!empty($_FILES['userfile']['name']))
                    {

                        if (!$this->upload->do_upload())
                        {
                            return false;

                        } else {
                            $uploaded_path[$i] = $this->upload->data('file_name');
                            $this->upload->initialize($config);
                        }
                    }else {
                        return false;
                    }


            }
            return $uploaded_path;

        }

        public function time2str($ts) {
            if(!ctype_digit($ts)) {
                $ts = strtotime($ts);
            }
            $diff = time() - $ts;
            if($diff == 0) {
                return 'now';
            } elseif($diff > 0) {
                $day_diff = floor($diff / 86400);
                if($day_diff == 0) {
                    if($diff < 60) return 'just now';
                    if($diff < 120) return '1 minute ago';
                    if($diff < 3600) return floor($diff / 60) . ' minutes ago';
                    if($diff < 7200) return '1 hour ago';
                    if($diff < 86400) return floor($diff / 3600) . ' hours ago';
                }
                if($day_diff == 1) { return 'Yesterday'; }
                if($day_diff < 7) { return $day_diff . ' days ago'; }
                if($day_diff < 31) { return ceil($day_diff / 7) . ' weeks ago'; }
                if($day_diff < 60) { return 'last month'; }
                return date('F Y', $ts);
            } else {
                $diff = abs($diff);
                $day_diff = floor($diff / 86400);
                if($day_diff == 0) {
                    if($diff < 120) { return 'in a minute'; }
                    if($diff < 3600) { return 'in ' . floor($diff / 60) . ' minutes'; }
                    if($diff < 7200) { return 'in an hour'; }
                    if($diff < 86400) { return 'in ' . floor($diff / 3600) . ' hours'; }
                }
                if($day_diff == 1) { return 'Tomorrow'; }
                if($day_diff < 4) { return date('l', $ts); }
                if($day_diff < 7 + (7 - date('w'))) { return 'next week'; }
                if(ceil($day_diff / 7) < 4) { return 'in ' . ceil($day_diff / 7) . ' weeks'; }
                if(date('n', $ts) == date('n') + 1) { return 'next month'; }
                return date('F Y', $ts);
            }
        }

         function generate_account_no(){
            mt_srand((double)microtime()*10000);
            $charid = md5(uniqid(rand(), true));
            $c = unpack("C*",$charid);
            $c = implode("",$c);

            return substr($c,0,6);
    }

    function check_timestamp($time) {
        $con['conditions'] = array(
            'timestamp' => $time
        );
        $con['returnType'] = 'single';
        $time = $this->Common_Model->getRows($con, 't_timestamp');
        if ($time) {
            return true;
        } else {
            return false;
        }
    }
}
