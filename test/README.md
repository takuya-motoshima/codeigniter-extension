# CodeIgniterExtensionTest

## DB
```
mysql -u root -proot -D ciex;

CREATE SCHEMA IF NOT EXISTS `ciex` DEFAULT CHARACTER SET utf8mb4;

CREATE TABLE `ciex`.`user` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` text NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE `ciex`.`salary`;
CREATE TABLE `ciex`.`salary` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `userId` int unsigned NOT NULL,
    `posted` date NOT NULL,
    `salary` int unsigned NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `ukSalary1` (`userId`, `posted`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ciex.user(name) VALUES
  ('foo'),
  ('bar');

INSERT INTO ciex.salary(userId, posted, salary) VALUES
  (1, '2019-01-01', 100000),
  (1, '2019-01-02', 100000),
  (1, '2019-01-03', 100000),
  (2, '2019-01-01', 100000),
  (2, '2019-01-02', 100000);

