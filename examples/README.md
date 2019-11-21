# codeigniter-extension/test

## DB
```
mysql -u root -D test;

CREATE SCHEMA IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8mb4;
use test;

DROP TABLE `user`;
CREATE TABLE `user` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ukUser1` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE `salary`;
CREATE TABLE `salary` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `userId` int unsigned NOT NULL,
    `payday` date NOT NULL,
    `salary` int unsigned NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `ukSalary1` (`userId`, `payday`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ikSession1` (`timestamp`)
);

INSERT INTO test.user(name) VALUES ('test');

INSERT INTO test.salary(userId, payday, salary) VALUES
  (1, '2019-07-31', 500000),
  (1, '2019-08-31', 1000000);
