# CodeIgniterExtensionTest

## DB
```
mysql -u root -proot;
CREATE SCHEMA IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8mb4;
CREATE TABLE `test`.`user` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `password` text NOT NULL,
    `name` text NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
