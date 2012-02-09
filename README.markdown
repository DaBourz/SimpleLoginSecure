#SimpleLogin Secure
**Name: SimpleLoginSecure 2.0**  
**Released: Feb 8, 2012**  
**CI Version: Tested with CodeIgniter 2.1.0**  
**Author: Stéphane Bourzeix**  

_SimpleLogin-Secure was written by Alex Dunae._  
_SimpleLogin-Secure for Code Igniter is a modified version of Anthony Graddy’s Simplelogin library._  
_SimpleLogin-Secure version 2 is by Stéphane Bourzeix from Alex Dunae's code._  

* ChangeLog:  
  * Upgraded to use the PHPASS version 0.3  
  * Changed the "getwhere()" calls to "get_where()" for Code Igniter 2.0 compatibility.  


In Anthony’s words:  

>Simplelogin is designed to give you a quick and simple login library that will get you up and running with an unobtrusive authorization system very quickly. It does not try to guess how you want to structure your app, it simply tries to give you a little help.

There are three primary modifications to Anthony’s original code.  Most importantly, SimpleLogin-Secure uses the phpass framework for secure, portable password hashing instead of straight md5 without a salt.  Secondly, SimpleLogin-Secure uses an e-mail address instead of a user name as the login key.  And finally, it adds user_date, user_modified and user_last_login date/time fields to the default install.

For more information on why md5 hashing is not enough, see the excellent post about password schemes on the Matasano Security blog.

Installation and configuration
Copy SimpleLoginSecure.php and the entire phpass-0.3 directory to your application/libraries directory.

Create your database table using the following SQL sample.  You can also edit the hash length and portability constants at the top of SimpleLoginSecure.php.

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


Use  
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


_Credits_  
_The original Simplelogin library was written by Anthony Graddy._    
_SimpleLogin-Secure was written by Alex Dunae, 2008._  
_SimpleLogin-Secure new version, 2.0, for Code Igniter II by Stéphane Bourzeix 2011/2012._
