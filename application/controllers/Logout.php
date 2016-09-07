<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends PNR_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('authentication');
        $this->data['is_logged_in'] = $this->authentication->is_loggedin();
    }
    
    public function index() {
        $this->load->helper('url');
        $this->authentication->logout();
        redirect();
    }
    
    public function test() {
        var_export($this->data['is_logged_in']);
    }
}