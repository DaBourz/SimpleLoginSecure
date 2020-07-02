# Sample table structure for the SimpleLoginSecure CodeIgniter Library.
# Version for MySQL 5.0+

DROP TABLE IF exists `users`;

CREATE TABLE `users` (
  `user_id` INTEGER NOT NULL DEFAULT 00000000000000, -- must use gen id in php code
  `user_email` TEXT NOT NULL DEFAULT '', -- compat with all dbs, but cannot be empty
  `user_pass` TEXT NOT NULL DEFAULT '', -- compat with all dbs, but cannot be empty
  `user_date` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',  -- compat with all dbs
  `user_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',  -- compat with all dbs
  `user_last_login` DATETIME NULL DEFAULT '1000-01-01 00:00:00', -- compat with all dbs
  PRIMARY KEY  (`user_id`)
);
