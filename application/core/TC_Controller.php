<?php

class TC_Controller extends CI_Controller {

    public $data = array();

    public function __construct() {
        parent::__construct();
        // added session here and removed from autoload, because we dont want session all the time
        $this->load->library('session');
        $this->load->library(!$this->config->item('firephp_plugin_enabled')?'firephp_fake':'firephp', NULL, 'firephp');
    }
    
    public function detect_language() {
        $supported_languages = array('en', 'ro');
        $title_languages = array('en' => 'english', 'ro' => 'romana');
        if (!empty($_GET['language'])) {
            $lang = $_GET['language'];
        }
        else if (!empty($this->session->userdata('language'))) {
            $lang = $this->session->userdata('language');
        }
        else if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            log_message('debug', 'Checking browser languages: '.implode(', ', $accept_langs));
            foreach ($accept_langs as $lang) {
                $lang = substr($lang, 0, 2);// Turn en-gb into en
                if (in_array($lang, $supported_languages)) {
                    break;
                }
            }
        }
        if (empty($lang) or !in_array($lang, $supported_languages)) {
            $lang = 'ro';
        }
        $this->data['lang'] = $lang;
        $this->session->set_userdata(array('language' => $lang));

        if (!empty($_GET['language'])) {
            secure_redirect($this->uri->uri_string());
            return true;
        }

        $this->lang->lang_code = $lang;
        $this->lang->load('general', $title_languages[$lang]);
        $this->lang->load('form_validation', $title_languages[$lang]);
        //$this->config->set_item('language', $title_languages[$lang]); 

        $this->data['page-layout'] = 'boxed'; // Page Layout (values: 'boxed', 'wide')
        $this->data['meta_title'] = 'Partidul Noua Romanie';
        $this->data['meta_author'] = 'Partidul Noua Romanie';
        $this->data['meta_keywords'] = $this->lang->line('meta_keywords');
        $this->data['meta_description'] = $this->lang->line('meta_description');
    }
}
