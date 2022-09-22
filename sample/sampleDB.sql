CREATE DATABASE IF NOT EXISTS `sample` DEFAULT CHARACTER SET utf8mb4;
USE `sample`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` enum('admin', 'member') NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `name` varchar(30) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukAccountEmail` (`email`)
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

-- NOTE: The user's login password is a hash of the string "password".
INSERT INTO `user` (`role`, `email`, `password`, `name`) VALUES
  ('admin', 'robin@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Robin'),
  ('member', 'taylor@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Taylor'),
  ('member', 'vivian@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Vivian'),
  ('member', 'harry@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Harry'),
  ('member', 'eliza@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Eliza'),
  ('member', 'nancy@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Nancy'),
  ('member', 'melinda@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Melinda'),
  ('member', 'harley@example.com', 'c041762918835dea95fb539495c93675c4818dfc60c6cdb2f878a2158ed59016', 'Harley');