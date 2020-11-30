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
 * DROP TABLE IF exists `nomina_users`;
 * CREATE TABLE `nomina_users` (
 *   `user_id` INTEGER unsigned NOT NULL DEFAULT 00000000000000,
 *   `user_email` TEXT NOT NULL DEFAULT '',
 *   `user_pass` TEXT NOT NULL DEFAULT '',
 *   `user_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
 *   `user_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
 *   `user_last_login` DATETIME NULL DEFAULT '1000-01-01 00:00:00',
 *   PRIMARY KEY  (`user_id`)
 * );
 * 
 * @package   SimpleLoginSecure
 * @version   2.1.1
 * @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
 * @copyright Copyright (c) 2012-2013, Stéphane Bourzeix
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      https://github.com/DaBourz/SimpleLoginSecure
 */
class SimpleLoginSecure
{
	protected $CI; // CodeIgniter object
	protected $user_table = 'users'; // Table name
	
	/**
	* Constructor
	* Get the current CI object
	*/
	public function __construct()
    {
        // Assign the CodeIgniter super-object
		$this->CI =& get_instance();
	}


	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create($user_email = '', $user_pass = '', $auto_login = true) 
	{

		//Make sure account info was sent
		if($user_email == '' OR $user_pass == '') {
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('user_email', $user_email); 
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0) //user_email already exists
			return false;

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);

		//Insert account into the database
		$data = array(
					'user_id' => date('YmdHis'),
					'user_email' => $user_email,
					'user_pass' => $user_pass_hashed,
					'user_date' => date('c'),
					'user_modified' => date('c'),
				);

		$this->CI->db->set($data); 

		if(!$this->CI->db->insert($this->user_table)) //There was a problem! 
			return false;						
				
		if($auto_login)
			$this->login($user_email, $user_pass);
		
		return true;
	}

	/**
	 * Update a user account
	 *
	 * Only updates the email, just here for you can 
	 * extend / use it in your own class.
	 *
	 * @access	public
	 * @param integer
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function update($user_id = null, $user_email = '', $auto_login = true) 
	{
		//Make sure account info was sent
		if($user_id == null OR $user_email == '') {
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('user_id', $user_id);
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() == 0){ // user don't exists
			return false;
		}
		
		//Update account into the database
		$data = array(
					'user_email' => $user_email,
					'user_modified' => date('c'),
				);
 
		$this->CI->db->where('user_id', $user_id);

		if(!$this->CI->db->update($this->user_table, $data)) //There was a problem! 
			return false;						
				
		if($auto_login){
			$user_data['user_email'] = $user_email;
			$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin
			
			$this->CI->session->set_userdata($user_data);
			}
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

		if($user_email == '' OR $user_pass == '')
			return false;


		//Check if already logged in
		if($this->CI->session->userdata('user_email') == $user_email)
			return true;
		
		
		//Check against user table
		$this->CI->db->where('user_email', $user_email); 
		$query = $this->CI->db->get_where($this->user_table);

		
		if ($query->num_rows() > 0) 
		{
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			if(!$hasher->CheckPassword($user_pass, $user_data['user_pass']))
				return false;

			//Create a fresh, brand new session
			if (CI_VERSION >= '3.0') {
				$this->CI->session->sess_regenerate(TRUE);
			} else {
				//Destroy old session
				$this->CI->session->sess_destroy();
				$this->CI->session->sess_create();
			}

			$data = array('user_last_login' => date('c'));
			$this->db->where('user_id', $user_data['user_id']); 
			$this->db->update($this->user_table, $data);

			//Set session data
			unset($user_data['user_pass']);
			$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin
			$user_data['logged_in'] = true;
			$this->CI->session->set_userdata($user_data);
			
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
		$this->CI->session->sess_destroy();
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
		
		if(!is_numeric($user_id))
			return false;			

		return $this->CI->db->delete($this->user_table, array('user_id' => $user_id));
	}
	
	
	/**
	* Edit a user password
	* @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
	* @author    Diego Castro <castroc.diego[at]gmail.com>
	*
	* @access  public
	* @param  string
	* @param  string
	* @param  string
	* @return  bool
	*/
	function edit_password($user_email = '', $old_pass = '', $new_pass = '')
	{
		
		// Check if the password is the same as the old one
		$this->CI->db->select('user_pass');
		$query = $this->CI->db->get_where($this->user_table, array('user_email' => $user_email));
		$user_data = $query->row_array();

		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);	
		if (!$hasher->CheckPassword($old_pass, $user_data['user_pass'])){ //old_pass is the same
			return FALSE;
		}
		
		// Hash new_pass using phpass
		$user_pass_hashed = $hasher->HashPassword($new_pass);
		// Insert new password into the database
		$data = array(
			'user_pass' => $user_pass_hashed,
			'user_modified' => date('c')
		);
		
		$this->CI->db->set($data);
		$this->CI->db->where('user_email', $user_email);
		if(!$this->CI->db->update($this->user_table, $data)){ // There was a problem!
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
}
?>
