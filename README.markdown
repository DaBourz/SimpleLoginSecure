#SimpleLogin Secure
**Name: SimpleLoginSecure 3.1.1**  
**Released: Feb 8, 2012**  
**Updated: Nov 30, 2020**  
**CI Version: Works now with CodeIgniter 2 and 3**  
**Author: Stéphane Bourzeix**  

_SimpleLogin-Secure was written by Alex Dunae._  
_SimpleLogin-Secure for Code Igniter is a modified version of Anthony Graddy’s Simplelogin library._  
_SimpleLogin-Secure version 2 and 3 are by Stéphane Bourzeix from Alex Dunae's code._  

* ChangeLog:  
  * Ugraded to work with BOTH CodeIgniter 2.0 AND 3.0
  * Added a version test and use "sess regenerate" or "sess create" depending on version
  * Added a new SQL file for creating the table for newer versions of MYSQL
  * Upgraded to use the PHPASS version 0.3  
  * Changed the "getwhere()" calls to "get_where()" for Code Igniter 2.0 compatibility.  
  * Added the Update function to allow to change the user's email from your classes.
  * Added the edit_password function to compare and change passwords
  * Removed the Now() MySQL call and using PHP date() instead
  * Bug fixes
  * Added TAG v2.1.2 as a reference to the old version for CI 2
  * Added TAG v3 as a reference to Code Igniter 3
  * Updated PHPASS to both last versions for any PHP, please choose on top of the file
  * Last version befopre CodeIgniter 4



In Anthony’s words:  

>Simplelogin is designed to give you a quick and simple login library that will get you up and running with an unobtrusive authorization system very quickly. It does not try to guess how you want to structure your app, it simply tries to give you a little help.

There are three primary modifications to Anthony’s original code.  Most importantly, SimpleLogin-Secure uses the phpass framework for secure, portable password hashing instead of straight md5 without a salt.  Secondly, SimpleLogin-Secure uses an e-mail address instead of a user name as the login key.  And finally, it adds user_date, user_modified and user_last_login date/time fields to the default install.

For more information on why md5 hashing is not enough, see the excellent post about password schemes on the Matasano Security blog.

**Installation and configuration :**

Copy SimpleLoginSecure.php and the entire phpass-0.5 directory to your application/libraries directory.
If you're on PHP 3 to 5 use PHPASS 4 and change the line of the require at the top of the file.

Create your database table using the following SQL sample. SQL files are provided.  
You can also edit the hash length and portability constants at the top of SimpleLoginSecure.php.

    CREATE TABLE `users` (
      `user_id` int(10) unsigned NOT NULL auto_increment,
      `user_email` varchar(255) NOT NULL default '',
      `user_pass` varchar(60) NOT NULL default '',
      `user_date` datetime NOT NULL default '0000-00-00 00:00:00',
      `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
      `user_last_login` datetime NULL default NULL,
       PRIMARY KEY  (`user_id`),
       UNIQUE KEY `user_email` (`user_email`)
    ) DEFAULT CHARSET=utf8; 

or for newer versions of MYSQL (>=5.6) use :

	CREATE TABLE `users` (
  		`user_id` int(10) unsigned NOT NULL auto_increment,
  		`user_email` varchar(255) NOT NULL default '',
  		`user_pass` varchar(60) NOT NULL default '',
  		`user_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  		`user_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  		`user_last_login` datetime DEFAULT NULL,
  	PRIMARY KEY  (`user_id`),
  	UNIQUE KEY `user_email` (`user_email`)
	) DEFAULT CHARSET=utf8;

**Use**  

The methods exposed by SimpleLogin-Secure are identical to those of Simplelogin.

    // load the library
    $this->load->library('SimpleLoginSecure');

    // create a new user
    $this->simpleloginsecure->create('user@example.com', 'uS$rpass!');

    // attempt to login
    if($this->simpleloginsecure->login('user@example.com', 'uS$rpass!')) {
        // success
    }

    // check if logged in
    if($this->session->userdata('logged_in')) {
        // logged in
    }

    // logout
    $this->simpleloginsecure->logout();

    // delete by user ID
    $this->simpleloginsecure->delete($user_id); 

	// Update user Email
	$this->simpleloginsecure->update($user_id, $user_email, $auto_login);

	// Update Password
	$this->simpleloginsecure->edit_password($user_email, $old_pass, $new_pass)



_Credits_  
_The original Simplelogin library was written by Anthony Graddy._    
_SimpleLogin-Secure was written by Alex Dunae, 2008._  
_SimpleLogin-Secure new version, 2.0, for Code Igniter II by Stéphane Bourzeix 2011/2013._
_SimpleLogin-Secure new version, 3.0, for Code Igniter II and III by Stéphane Bourzeix 2020._
