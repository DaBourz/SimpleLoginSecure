# Sample table structure for the SimpleLoginSecure CodeIgniter Library.
# Version for MySQL 5.6 and more

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