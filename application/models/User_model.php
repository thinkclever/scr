<?php

/**
 * User model
 */
class User_model extends PNR_Model {

    public $table_name = 'users';
    public $primary_key = 'id';
    public $token_table = 'tokens';

    /**
     * constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Check if a username exists in the database before updating user profile
     * @param $username string
     * @param $except_user_id null|int
     * @return bool
     */
    public function username_exists($username, $except_user_id = null){
        $except_user_id_clause = is_numeric($except_user_id) ? "AND id != {$except_user_id}" : '';
        $q = "SELECT `id` FROM {$this->table_name} WHERE `username` LIKE '".$this->db->escape_like_str($username)."' ".$except_user_id_clause;
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }
        return !empty($this->db->query($q)->first_row()->id);
    }
    
    public function email_exists($email, $except_user_id = null){
        $except_user_id_clause = is_numeric($except_user_id) ? "AND id != {$except_user_id}" : '';
        $q = "SELECT `id` FROM {$this->table_name} WHERE `email` LIKE '".$this->db->escape_like_str($email)."' ".$except_user_id_clause;
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }
        return !empty($this->db->query($q)->first_row()->id);
    }
    
    /**
     * Get username for that email
     * @param type $email
     * @return string
     */
    public function get_username_from_email($email) {
        $email = email_filter($email);
        if (!$email) {
            return false;
        }
        $q = "SELECT `username` FROM `{$this->table_name}` WHERE `email` LIKE '{$email}'";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }

        return $this->db->query($q)->first_row()->username;
    }
    
    public function get_userid_from_email($email) {
        $email = email_filter($email);
        if (!$email) {
            return false;
        }
        $q = "SELECT `id` FROM `{$this->table_name}` WHERE `email` LIKE '{$email}'";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }

        return $this->db->query($q)->first_row()->id;
    }
    
    /**
     * Mark user online as last seen now
     * @param type $user_id
     * @return boolean
     */
    public function last_seen_now($user_id = '') {
        $user_id = number_filter($user_id);
        if (!$user_id) {
            return false;
        }
    	$this->db->query("UPDATE {$this->table_name} SET `last_seen` = NOW() WHERE `id` = '{$user_id}'");
    }
    
    public function confirm_user($user_id = '') {
        $user_id = number_filter($user_id);
        if (!$user_id) {
            return false;
        }
    	$this->db->query("UPDATE {$this->table_name} SET `is_confirmed` = 1 WHERE `id` = '{$user_id}'");
    }
    
    public function is_admin($user_id = '') {
        $user_id = number_filter($user_id);
        if (!$user_id) {
            return false;
        }

        $q = "SELECT `user_type` FROM `{$this->table_name}` WHERE `id` = '{$user_id}'";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }

        $user_type = $this->db->query($q)->first_row()->user_type;
        return (($user_type & USER_TYPE_ADMIN) == USER_TYPE_ADMIN);
    }
    
    /**
     * Get user_id from recovery_token
     * @staticvar array $ui4rectoken
     * @param type $recovery_token
     * @return boolean|array
     */
    public function get_userid_from_recovery_token($recovery_token = '') {
        static $ui4rectoken = array(); if (isset($ui4rectoken[$recovery_token])) { return $ui4rectoken[$recovery_token]; }

        $recovery_token = preg_replace('/[^0-9A-Za-z]/', '', $recovery_token);
        if (empty($recovery_token)) {
            return false;
        }

        $q = "SELECT `id` FROM {$this->table_name} WHERE `recovery_token` = UNHEX('{$recovery_token}')";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }

        $ui4rectoken[$recovery_token] = $this->db->query($q)->first_row()->id;
        return $ui4rectoken[$recovery_token];
    }
    
    /**
     * Get invitation_token from user_id
     * @staticvar array $itok4ui
     * @param type $user_id
     * @return boolean|array
     */
    public function get_itok_from_userid($user_id = '') {
        static $itok4ui = array();
        if (isset($itok4ui[$user_id])) { return $itok4ui[$user_id]; }

        $user_id = number_filter($user_id);
        if (!$user_id) {
            return false;
        }

        $q = "SELECT HEX(`invitation_token`) AS itok, `invitation_token` FROM {$this->table_name} WHERE `id` = '{$user_id}'";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }

        $rec = $this->db->query($q)->first_row();
        if (empty($rec->invitation_token)) {
            return false;
        }

        $itok4ui[$user_id] = $rec->itok;
        return $itok4ui[$user_id];
    }
    
    public function get_userid_from_invitation_token($invitation_token = '') {
        static $ui4itok = array();
        if (isset($ui4itok[$invitation_token])) { return $ui4itok[$invitation_token]; }

        $invitation_token = preg_replace('/[^0-9A-Za-z]/', '', $invitation_token);
        if (empty($invitation_token)) {
            return false;
        }

        $q = "SELECT `id` FROM `{$this->table_name}` WHERE `invitation_token` = UNHEX('{$invitation_token}')";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }
        
        $ui4itok[$invitation_token] = $this->db->query($q)->first_row()->id;
        return $ui4itok[$invitation_token];
    }
    
    public function get_userid_from_fb_uid($fb_uid = '') {
        static $ui4fbuid = array();
        if (isset($ui4fbuid[$fb_uid])) {
            return $ui4fbuid[$fb_uid];  
        }

        $fb_uid = preg_replace('/[^0-9A-Za-z]/', '', $fb_uid);
        if (empty($fb_uid)) {
            return false;
        }

        $q = "SELECT `id` FROM {$this->table_name} WHERE `fb_uid` = '{$fb_uid}'";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }

        $ui4fbuid[$fb_uid] = $this->db->query($q)->first_row()->id;
        return $ui4fbuid[$fb_uid];
    }
    
    public function get_age($user_id = '') {
        $user_id = number_filter($user_id);
        if (empty($user_id)) {
            return false;
        }
        
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        if ($this->cache->memcached->is_supported()) {
            $rec = $this->cache->get('age_uid_'.$user_id);
            if (!empty($rec)) {
                return $rec;
            }
        }

        $rec_arr = $this->db->query("SELECT `birth_year`,`birth_month`,`birth_day` FROM `{$this->table_name}` WHERE `id` = '{$user_id}' LIMIT 0,1")->result_array();
        if (!is_array($rec_arr[0]) || !isset($rec_arr[0]['birth_year'])) {
            return false;
        }

        $birth = $rec_arr[0]['birth_year'].'-'.str_pad($rec_arr[0]['birth_month'], 2, '0', STR_PAD_LEFT).'-'.str_pad($rec_arr[0]['birth_day'], 2, '0', STR_PAD_LEFT).' 12:00:00';
        $date = new DateTime($birth);
        $now = new DateTime();
        $age = $now->diff($date);
        $this->cache->save('age_uid_'.$user_id, $age->y, 600 /* 10 minutes */);
        return $age->y;
    }
    
    public function get_username($user_id = '') {
        $user_id = number_filter($user_id);
        if (empty($user_id)) {
            return false;
        }
        
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        if ($this->cache->memcached->is_supported()) {
            $rec = $this->cache->get('uname4uid_'.$user_id);
            if (!empty($rec)) {
                return $rec;
            }
        }

        $q = "SELECT `username` FROM `{$this->table_name}` WHERE `id` = '{$user_id}' LIMIT 0,1";
        $rec_arr = $this->db->query($q)->result_array();
        if (!is_array($rec_arr[0]) || !isset($rec_arr[0]['username'])) {
            return false;
        }

        $this->cache->save('uname4uid_'.$user_id, $rec_arr[0]['username'], 600 /* 10 minutes */);
        return $rec_arr[0]['username'];
    }
    
   public function get_name($user_id = '') {
        $user_id = number_filter($user_id);
        if (empty($user_id)) {
            return false;
        }
        
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        if ($this->cache->memcached->is_supported()) {
            $rec = $this->cache->get('name4uid_'.$user_id);
            if (!empty($rec)) {
                return $rec;
            }
        }

        $q = "SELECT `firstname`, `lastname` FROM `{$this->table_name}` WHERE `id` = '{$user_id}' LIMIT 0,1";
        $rec_arr = $this->db->query($q)->result_array();
        if (!is_array($rec_arr[0]) || !isset($rec_arr[0]['firstname'])) {
            return false;
        }

        $this->cache->save('name4uid_'.$user_id, $rec_arr[0]['firstname'].' '.$rec_arr[0]['lastname'], 600 /* 10 minutes */);
        return $rec_arr[0]['firstname'].' '.$rec_arr[0]['lastname'];
    }
    
    public function get_pic($user_id = '') {
        $user_id = number_filter($user_id);
        if (empty($user_id)) {
            return false;
        }

        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        if ($this->cache->memcached->is_supported()) {
            $rec = $this->cache->get('pic4uid_'.$user_id);
            if (!empty($rec)) {
                return $rec;
            }
        }
        
        $rec_arr = $this->db->query("SELECT `profile_picture_id` FROM `{$this->table_name}` WHERE `id` = '{$user_id}' LIMIT 0,1")->result_array();
        if (!is_array($rec_arr[0]) || !isset($rec_arr[0]['profile_picture_id'])) {
            return false;
        }
        $this->load->model('picture_model');
        $profile_picture = $this->picture_model->get_pic_details($rec_arr[0]['profile_picture_id']);
        $this->cache->save('pic4uid_'.$user_id, $profile_picture['thumbnail'], 600 /* 10 minutes */);
        return $profile_picture['thumbnail'];
    }
    
    /**
     * $this->load->model('user_model');
     * $user_id = $this->user_model->get_userid_from_username($user);
     * @param type $username
     * @return string or false
     */
    public function get_userid_from_username($username) {
        if (!$username) {
            return false;
        }
        
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        if ($this->cache->memcached->is_supported()) {
            $rec = unserialize($this->cache->get('uid4uname_'.$username));
            //$this->firephp->export('memcached:'.var_export($rec_arr,1));
            if (!empty($rec)) {
                return $rec;
            }
        }

        $rec = $this->db->query("SELECT `id` FROM `{$this->table_name}` WHERE `username` = '{$username}' LIMIT 0,1")->first_row()->id;
        if (empty($rec)) {
            return false;
        }
        
        $this->cache->save('uid4uname_'.$username, $rec, 600 /* 10 minutes */);
        return $rec;
    }
    
    public function get_user_details($user_id = '') {
        $user_id = number_filter($user_id);
        if (empty($user_id)) {
            return false;
        }
        $details = $this->get_records(array(
            'fields' => array('username','points','language','country_id','county_id','profile_picture_id','birth_year','birth_month','birth_day',
            'looks4friends','looks4fun','looks4dating','looks4love','looks4sex','looks4marriage','looks4chat','looks4surfcontent'),
            'and_filters' => array('id' => $user_id),
            'assoc_keys' => true,
        ));
        return isset($details[0]) ? $details[0] : false;
    }
    
    public function get_online_users() {
        $q = "SELECT `id`,`username`,`profile_picture_id` FROM `{$this->table_name}` WHERE `last_seen` > SUBTIME(NOW(),'0:3:0')";
        if (!$this->db->query($q)->num_rows()) {
            return false;
        }
        $rec_arr = $this->db->query($q)->result_array();
        return $rec_arr;
    }
}