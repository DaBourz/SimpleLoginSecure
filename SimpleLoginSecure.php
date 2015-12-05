<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.3/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * SimpleLoginSecure Class
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using 
 * this setup you may need to do some tweaking.
 *   
 * 
 *   CREATE TABLE `users` (
 *     `user_id` int(10) unsigned NOT NULL auto_increment,
 *     `user_email` varchar(255) NOT NULL default '',
 *     `user_pass` varchar(60) NOT NULL default '',
 *     `user_date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation date',
 *     `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
 *     `user_last_login` datetime NULL default NULL,
 *     `user_withdrawal` int(11) DEFAULT '0',
 *     `user_withdrawal_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 *     PRIMARY KEY  (`user_id`),
 *     UNIQUE KEY `user_email` (`user_email`),
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 *
 *
 * @package   SimpleLoginSecure
 * @version   2.0
 * @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
 * @copyright Copyright (c) 2012, Stéphane Bourzeix
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      https://github.com/DaBourz/SimpleLoginSecure
 */
class SimpleLoginSecure
{
	var $CI;
	var $user_table;
	var $prefix_key;

	function __construct($config=array()){
		$this->config = $config;

		// name of user table
		$this->user_table = $config['user_table'];
		// prefix for name of session
		$this->prefix_key = $this->config['prefix_key'];
	}


	/**
	 * Check a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function check($user_email = '', $user_pass = '', $auto_login = true) 
	{
		$this->CI =& get_instance();

		//Make sure account info was sent
		if($user_email == '' OR $user_pass == '') {
			$result = false;
		}else{
			$result = true;
		}
		
		//Check against user table
		$query = $this->CI->db->get_where($this->user_table, array('user_email' => $user_email, 'user_withdrawal' => 0));

		if ($query->num_rows() > 0){ //user_email already exists
			$result = false;
		}else{
			$result = true;
		}

			return $result;
	}

	/**
	 * Hash user_pass using phpass
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function hash_password($user_pass){
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);

		return $user_pass_hashed;
	}

	/**
	 * Create or Update a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create($user_email = '', $user_pass = '', $type="insert", $user_id="", $auto_login = true) 
	{
		$this->CI =& get_instance();

		// check duplication
		$return_flg = $this->check($user_email, $user_pass, $auto_login = true);
		if($return_flg!=1){ return false; }

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);

		// insert
		if($type=='insert'){
			//Insert account into the database
			$data = array(
					'user_email' => $user_email,
					'user_pass' => $user_pass_hashed,
					'user_date' => date('c'),
					'user_modified' => date('c'),
					);

			$this->CI->db->set($data); 

			if(!$this->CI->db->insert($this->user_table)) //There was a problem! 
				return false;						

		// update	
		}elseif($type=='update'){
			// update data
			$data = array('user_pass' => $user_pass_hashed);

			$this->CI->db->where('user_id', $user_id);
			if(!$this->CI->db->update($this->user_table, $data)) //There was a problem!
				return false;
		}

		if($auto_login)
			$this->login($user_email, $user_pass);
		
		return true;
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($user_email = '', $user_pass = '') 
	{
		$this->CI =& get_instance();

		if($user_email == '' OR $user_pass == '')
			return false;


		//Check if already logged in
		if($this->CI->session->userdata('user_email') == $user_email)
			return true;
		
		
		//Check against user table
		$query = $this->CI->db->get_where($this->user_table, array('user_email' => $user_email, 'user_withdrawal' => 0));

		if ($query->num_rows() > 0) 
		{
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			if(!$hasher->CheckPassword($user_pass, $user_data['user_pass']))
				return false;

			//Destroy old session
			$this->CI->session->sess_destroy();
			
			//Create a fresh, brand new session
			$this->CI->session->sess_create();

			$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET user_last_login = NOW() WHERE user_id = ' . $user_data['user_id']);

			//Set session data
			unset($user_data['user_pass']);
			$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin
			$user_data['logged_in'] = true;

			// session key
			if(!empty($this->prefix_key)){
				$this->CI->session->set_userdata($this->prefix_key, $user_data);
			}else{
				$this->CI->session->set_userdata($user_data);
			}

			return true;
		} 
		else 
		{
			return false;
		}	

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();		

		//Destroy old session
		if(!empty($this->prefix_key)){
			$this->CI->session->unset_userdata($this->prefix_key);
		}else{
			$this->CI->session->sess_destroy();
		}
	}

	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete($user_id) 
	{
		$this->CI =& get_instance();
		
		if(!is_numeric($user_id))
			return false;			

		//Destroy old session
		$this->CI->session->sess_destroy();

		// soft delete
		$data = array('user_withdrawal' => 1, 'user_withdrawal_date' => date('Y-m-d H:i:s'));
		$this->CI->db->where('user_id', $user_id);
		if(!$this->CI->db->update($this->user_table, $data)){ //There was a problem!
				return false;
		}else{
				return true;
		}
	}
	
	/**
	 * Check current registered password.
	 *
	 * @access	public
	 * @param string
	 * @param string
	 * @return	bool
	 */
	function check_current_pass($user_email = '', $user_pass = '') 
	{
		$this->CI =& get_instance();

		if($user_email == '' OR $user_pass == '')
			return false;

		//Check against user table
		$query = $this->CI->db->get_where($this->user_table, array('user_email' => $user_email, 'user_withdrawal' => 0));

		
		if ($query->num_rows() > 0) 
		{
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			return $hasher->CheckPassword($user_pass, $user_data['user_pass']);
		}
		
		return false;
	}

}
?>
