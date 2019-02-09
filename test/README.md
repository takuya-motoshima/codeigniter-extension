# CodeIgniterExtensionTest

## DB
```
mysql -u root -proot -D ciexTest;
CREATE SCHEMA IF NOT EXISTS `ciexTest` DEFAULT CHARACTER SET utf8mb4;
CREATE TABLE `ciexTest`.`user` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` text NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
