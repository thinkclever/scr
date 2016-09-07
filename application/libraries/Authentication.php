<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('hex_pseudo_bytes')) {
    function hex_pseudo_bytes($l = 8) {
        if (function_exists('openssl_random_pseudo_bytes') && version_compare(PHP_VERSION, '5.3.4', '>=')) {
            $tmp = unpack('H*', openssl_random_pseudo_bytes($l/2));
            return array_shift($tmp);
        }
        else if (defined('MCRYPT_DEV_URANDOM') && ($seed = mcrypt_create_iv($l/2, MCRYPT_DEV_URANDOM)) !== FALSE) {
            $tmp = unpack('H*', $seed);
            return array_shift($tmp);
        }
        else {
            $tmp = '0123456789abcdef';
            $l -= 1; $r = $tmp{mt_rand(0,15)};
            while ($l-->0) { $r.=$tmp{mt_rand(0,15)}; }
            return $r;
        }
    }
}

/**
 * Authentication Class
 *
 * Very basic user authentication for CodeIgniter.
 * 
 * @package		Authentication
 * @version		1.0
 * @author		Modified by c4xp
 * @link		https://github.com/joelvardy/Basic-CodeIgniter-Authentication
 * 
 * If the `username_field` in your config is not set to 'email' you will want to allow NULL here
 * Ensure you have setup an encryption key and have configured CodeIgniter to use a database.
 *
 * $this->load->library('authentication'); // in each controller where you will use it OR autoload it
 *
 */
class Authentication {

    /**
     * CodeIgniter
     *
     * @access	private
     */
    private $ci;

    /**
     * Config items
     *
     * @access	private
     */
    private $user_table;
    private $token_table;
    private $id_field;
    private $username_field;
    private $password_field;
    private $realm;

    /**
     * Constructor
     */
    public function __construct() {
        
        log_message('debug', "Authentication Class Initialized");

        // Assign CodeIgniter object to $this->ci
        $this->ci = & get_instance();

        // Set config items
        $this->user_table = 'users';
        $this->token_table = 'tokens';
        $this->id_field = 'id';
        $this->username_field = 'username';
        $this->password_field = 'password';
        $this->realm = $this->ci->config->item('rest_realm');

        // Load database
        $this->ci->load->database();

        // Make sure we load sessions
        $this->ci->load->library('session');
    }

    /**
     * Check whether the username is unique
     *
     * @access	public
     * @param	string [$username] The username to query
     * @return	boolean
     */
    public function username_check($username) {
        // Read users where username matches
        $query = $this->ci->db->where($this->username_field, $username)->get($this->user_table);

        // If there are users
        if ($query->num_rows() > 0) {
            // Username is not available
            return false;
        }

        // No users were found
        return true;
    }
    
    public function get_realm() {
        return $this->realm;
    }

    /**
     * Create user
     *
     * @access	public
     * @param	string [$username] The username of the user to be created
     * @param	string [$password] The users password
     * @return	integer|boolean Either the user ID or FALSEupon failure
     */
    public function create_user($username, $password, $additionalData) {

        // Ensure username is available
        if (!$this->username_check($username)) {
            // Username is not available
            return false;
        }

        // Define data to insert
        $data = array(
            $this->username_field => $username,
            $this->password_field => custom_hash($username, $this->realm, $password)
        );
        
        $dataToInsert = array_merge($data, $additionalData);
        // Take out register_code as we dont have such column name, but use it later
        if (isset($dataToInsert['register_code'])) {
            $register_code = $dataToInsert['register_code'];
            unset($dataToInsert['register_code']);
        }
        else {
            $register_code = '';
        }

        // Always have a created date
        if (!isset($dataToInsert['created'])) {
            $is_ok = $this->ci->db
            ->set(array('created' => 'NOW()'), '', false)
            ->insert($this->user_table, $dataToInsert);
        }
        else {
            $is_ok = $this->ci->db
            ->insert($this->user_table, $dataToInsert);
        }
        
        // If inserting data fails, return false
        if (!$is_ok) {
            return false;
        }
        
        $user_id = $this->ci->db->insert_id();
        if (!empty($user_id)) {
            // Only send confirmation email if it's not confirmed already
            if (empty($dataToInsert['is_confirmed']) && !empty($register_code)) {
                $this->ci->db
                ->set(array('created' => 'NOW()'), '', false)
                ->set(array('token_code' => "UNHEX('".$register_code."')"), '', false)
                ->insert($this->token_table,
                    array('action_type' => '1'/*1=register(activate user_id)*/, 'action_id' => $user_id)
                );
            }
        }
        // Return user ID
        return (int) $user_id;
    }
    
    /**
     * Returns the hashed password, useful for digest authentication, in which case
     * the password should already be stored as md5(username:realm:password)
     * 
     * @access	public
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function get_hashed_password($username) {
        $user = $this->ci->db->select($this->password_field . ' as password')->where($this->username_field, $username)->get($this->user_table);
        if ($user->num_rows() == 0) {
            return false;
        }

        $user_details = $user->row();
        $HA1 = $user_details->password;
        return $HA1;
    }

    /**
     * Login
     *
     * @access	public
     * @param	string [$username] The username of the user to authenticate
     * @param	string [$password] The password to authenticate
     * @return	boolean Either TRUE or FALSE depending upon successful login
     */
    public function login($username, $password, $use_token = false) {
        // Select user details
        $user = $this->ci->db
                ->select($this->id_field . ' as id, ' . $this->password_field . ' as password')
                ->where($this->username_field, $username)
                ->get($this->user_table);

        // Ensure there is a user with that username
        if ($user->num_rows() == 0) {
            // There is no user with that username, but we won't tell the user that
            log_message('debug', 'There is no user with that username');
            return false;
        }

        // Set the user details
        $user_details = $user->row();

        // Do passwords match
        if (custom_hash($username, $this->realm, $password) == $user_details->password) {
            // Yes, the passwords match, Set the userdata for the current user
            return $this->getin($user_details->id, $use_token);
        }
        else {
            log_message('debug', 'The passwords do not match');
            return false;
        }
    }
    
    public function getin($id, $use_token = false) {
        $user = $this->ci->db
                ->select($this->username_field . ' as username, login_count, language, lastname, firstname, email')
                ->where($this->id_field, $id)
                ->get($this->user_table);

        if ($user->num_rows() == 0) {
            log_message('debug', 'There is no user with that id');
            return false;
        }

        // Set the user details
        $user_details = $user->row();
        $ip = $this->ci->input->ip_address();
        if ($use_token) {
            $auth_token = array_shift(unpack('H*', openssl_random_pseudo_bytes(16)));
            $this->ci->db->set('auth_token', "UNHEX('{$auth_token}')", false)->
            set('last_seen', date('Y-m-d H:i:s'))->
            set('ip', $ip)->
            set('login_count', $user_details->login_count + 1)->
            update($this->user_table, null, array($this->id_field => $id));
            $this->ci->input->set_cookie('CXACOK', $auth_token, 5184000 /* 60 days */);
        }
        else {
            $this->ci->db->update($this->user_table, array('last_seen' => date('Y-m-d H:i:s'), 'ip' => $ip, 'login_count' => $user_details->login_count + 1, 'auth_token' => null), array($this->id_field => $id));
        }

        log_message('debug', "Logged in {$user_details->username}");
        $this->ci->session->set_userdata(array(
            'id' => intval($id),
            'username' => $user_details->username,
            'language' => $user_details->language,
            'lastname' => $user_details->lastname,
            'firstname' => $user_details->firstname,
            'email' => $user_details->email,
            'ip' => $ip,
            'realm' => $this->realm
        ));
        return true;
    }

    /**
     * Check whether a user is logged in
     *
     * @access	public
     * @return	boolean TRUE for a logged in user otherwise FALSE
     */
    public function is_loggedin() {
        if (!$this->ci->session->userdata('id')) {
            return false;
        }
        if ($this->ci->session->userdata('realm') !== $this->realm) {
            $_SESSION = array();
            $this->ci->session->sess_destroy();
            return false;
        }
        return true;
    }

    /**
     * Read user details
     *
     * @access	public
     * @return	mixed or FALSE
     */
    public function read($key) {
        // Only allow us to read certain data
        if ($key == 'session_id') {
            return $this->ci->session->userdata('session_id');
        }

        // If the user is not logged in return false
        if (!$this->ci->session->userdata('id')) {
            return false;
        }

        if ($key == 'id') {
            // Return user id
            return (int) $this->ci->session->userdata('id');
        }
        else if ($key == 'username') {
            // Return username
            return (string) $this->ci->session->userdata('username');
        }
        else if ($key == 'language') {
            return (int) $this->ci->session->userdata('language');
        }
        else if ($key == 'lastname') {
            return (string) $this->ci->session->userdata('lastname');
        }
        else if ($key == 'firstname') {
            return (string) $this->ci->session->userdata('firstname');
        }
        else if ($key == 'email') {
            return (string) $this->ci->session->userdata('email');
        }
        else if ($key == 'ip') {
            return (string) $this->ci->session->userdata('ip');
        }

        // If nothing has been returned yet
        return false;
    }
    
    public function update($key, $value) {
        // If the user is not logged in return false
        if (!$this->is_loggedin()) {
            return false;
        }

        if ($key == 'username') {
            $this->ci->session->set_userdata(array('username' => $value));
            return true;
        }
        return true;
    }

    /**
     * Change password
     *
     * @access	public
     * @param	string [$password] The new password
     * @param	string [$user_id] The id of the user whos password will be changed, if none is set the current users password will be changed
     * @return	boolean Either TRUE or FALSE depending upon successful login
     */
    public function change_password($password, $user_id, $currentPassword) {
        // If no user id has been set
        if (!$user_id) {
            if ($this->is_loggedin()) {
                return false;// Ensure the current user is logged in
            }
            // Read the user id
            $user_id = $this->ci->session->userdata('id');
            $username = $this->ci->session->userdata('username');

            //original pass not valid
            if ($this->get_hashed_password($username) != custom_hash($username, $this->realm, $currentPassword)) {
                return false;
            }
        }
        
        if (!$this->ci->db->where($this->id_field, $user_id)->update($this->user_table, array($this->password_field => custom_hash($this->read('username'), $this->realm, $password)) )) {
            return false;
        }

        return true;
    }

    /**
     * Log a user out
     *
     * @access	public
     * @return	boolean Will always return TRUE
     */
    public function logout($delete_cookie = false) {
        // Remove auth_token from DB on forced logout
        if ($this->ci->session->userdata('id')) {
            $this->ci->db->update($this->user_table, array('auth_token' => null), array($this->id_field => $this->ci->session->userdata('id') ) );
        }
        // Remove userdata from cookie
        $this->ci->session->unset_userdata(array('id' => '', 'username' => '', 'language' => '', 'lastname' => '', 'firstname' => '', 'email' => ''));

        // destroy session data on server 
        $_SESSION = array();
        $this->ci->session->sess_destroy();

        $this->ci->input->set_cookie('CXACOK', '');
        
        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get('session.use_cookies') && $delete_cookie == TRUE) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        return true;
    }

    /**
     * Delete user account
     *
     * @access	public
     * @param	string [$user_id] The id of the user to delete
     * @return	boolean Either TRUE or FALSE depending upon successful login
     */
    public function delete_user($user_id) {

        // Update the users password
        if ($this->ci->db->where($this->id_field, $user_id)->delete($this->user_table)) {
            return true;
            // There was an error deleting the user
        } else {
            return false;
        }
    }
    
    /**
     * Automatic login based on cookie
     * @return boolean
     */
    public function got_token() {
        $auth_token = preg_replace('/[^0-9a-f]/', '', $this->ci->input->cookie('CXACOK', true));
        if (empty($auth_token)) {
            log_message('debug', "token empty");
            return false;
        }

        $user = $this->ci->db->select($this->id_field . ' as id, '.$this->username_field . ' as username, login_count, language, lastname, firstname, email')->where('auth_token', "UNHEX('{$auth_token}')", false)->get($this->user_table);
        if ($user->num_rows() == 0) {
            log_message('debug', "token invalid {$auth_token}");
            return false;
        }

        $user_details = $user->row();
        $this->ci->db->update($this->user_table, array('last_seen' => date('Y-m-d H:i:s'), 'ip' => $this->ci->input->ip_address(), 'login_count' => $user_details->login_count + 1), array($this->id_field => $user_details->id));

        log_message('debug', "Got token for {$user_details->id}");
        $this->ci->session->set_userdata(array(
            'id' => intval($user_details->id),
            'username' => $user_details->username,
            'language' => $user_details->language,
            'lastname' => $user_details->lastname,
            'firstname' => $user_details->firstname,
            'email' => $user_details->email,
            'realm' => $this->realm
        ));
        return true;
    }

    /**
     * Generates a 16 digit secret key in base32 format
     * @return string
     **/
    public static function generate_secret_key($length = 16) {
        mt_srand(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))&0x7fffff); // better seed the random number generator
        $b32 = '234567qwertyuiopasdfghjklzxcvbnm';
        $s = '';
        for ($i = 0; $i < $length; $i++) { $s .= $b32[mt_rand(0,31)]; }
        return $s;
    }
    
    public function generate_recovery_token($user_id) {
        if (!$user_id) {
            return false;
        }

        $recovery_token = array_shift(unpack('H*', openssl_random_pseudo_bytes(16)));
        log_message('debug', "Random recovery link {$recovery_token} for {$user_id}");

        $this->ci->db->set('recovery_token', "UNHEX('{$recovery_token}')", false)->update($this->user_table, null, array($this->id_field => $user_id));
        return $recovery_token;
    }
}

/* End of file Authentication.php */
/* Location: ./application/libraries/Authentication.php */