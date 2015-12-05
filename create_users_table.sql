# Sample table structure for the SimpleLoginSecure CodeIgniter Library.

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_email` varchar(255) NOT NULL default '',
  `user_pass` varchar(60) NOT NULL default '',
  `user_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_last_login` datetime NULL default NULL,
  `user_withdrawal` int(11) DEFAULT '0',
  `user_withdrawal_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) DEFAULT CHARSET=utf8;