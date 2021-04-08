# sample

This sample includes a simple user authentication process and a login page and dashboard.  


## Getting Started

See [../README.md](../README.md) for basic settings.  

Build the DB used by the sample application.  
Connect to the DB and add the DB and table.

```sql
CREATE DATABASE IF NOT EXISTS `sample` DEFAULT CHARACTER SET utf8mb4;

USE `sample`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` enum('admin', 'member') NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(30) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukAccount1` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  `email` varchar(255) DEFAULT NULL COMMENT 'A custom field dedicated to this sample application. The logged-in user name.',
  KEY `session_timestamp` (`timestamp`)
);

INSERT INTO `user` (`role`, `email`, `password`, `name`) VALUES
  ('admin', 'robin@example.com', 'password', 'Robin'),
  ('member', 'taylor@example.com', 'password', 'Taylor'),
  ('member', 'vivian@example.com', 'password', 'Vivian'),
  ('member', 'harry@example.com', 'password', 'Harry'),
  ('member', 'eliza@example.com', 'password', 'Eliza'),
  ('member', 'nancy@example.com', 'password', 'Nancy'),
  ('member', 'melinda@example.com', 'password', 'Melinda'),
  ('member', 'harley@example.com', 'password', 'Harley');
```

If the WEB server settings have been completed, the login page will be displayed from the following URL.  
https://<Your host name>