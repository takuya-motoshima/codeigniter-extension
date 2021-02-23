# Sample application

## Getting Started

Create project.  

```sh
mkdir -p /var/www/html;
cd /var/www/html;
composer create-project takuya-motoshima/codeIgniter-extension sampleapp;
```

Grant log, session, and cache write permissions to the web server.  

```sh
sudo chmod -R 755 /var/www/html/sampleapp/application/{logs,cache,session};
sudo chown -R nginx:nginx /var/www/html/sampleapp/application/{logs,cache,session};
```

Add the settings of the WEB server (nginx).  
Write the following in /etc/nginx/conf.d/sampleapp.conf.  

```nginx
server {
  listen       80;
  server_name  <Your host name>;
  ssi_last_modified on;
  ssi on;
  charset UTF-8;
  root /var/www/html/sampleapp/public;
  index index.php index.html;
  access_log  /var/log/nginx/sampleapp.access.log;
  error_log  /var/log/nginx/sampleapp.error.log  warn;
  gzip on;
  gzip_types application/json;

  # Codeigniter index file existence check
  location / {
    try_files $uri $uri/ /index.php;
    location = /index.php {
      fastcgi_pass  unix:/run/php-fpm/www.sock;
      include       fastcgi_params;
      fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
      fastcgi_param CI_ENV development;
    }
  }

  # Codeigniter request endpoint
  location ~ ((.*\.php)(/.*)|(\.php))$ {
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/run/php-fpm/www.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $request_filename;
  }

  # Static file access is cached
  location ~ .*\.(html?|jpe?g|gif|png|svg|css|js|ico|swf|inc) {
    # add_header Access-Control-Allow-Origin *;
    expires 1d;
    access_log off;
    #add_header X-Frame-Options DENY;
  }

  # If favicon does not exist, return an empty image
  location = /favicon.ico {
    empty_gif;
    access_log  off;
    log_not_found off;
  }

  # Checking access status
  location = /nginx_status {
    stub_status on;
    access_log off;
    allow 127.0.0.1;
    deny all;
  }
}
```

Reload Nginx.  

```sh
sudo systemctl reload nginx;
```

Create a DB.  

```sql
CREATE SCHEMA IF NOT EXISTS `codeigniter_extension_example` DEFAULT CHARACTER SET utf8mb4;
use codeigniter_extension_example;

-- User role master.
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `role` enum('admin','user') NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ukRole1` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User table.
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `role` enum('admin','user') NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` varchar(30) NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ukUser1` (`username`),
    CONSTRAINT `fkUser1` FOREIGN KEY (`role`) REFERENCES `role` (`role`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Login session.
DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
    `id` varchar(128) NOT NULL,
    -- `id` varchar(40) NOT NULL,
    `username` varchar(255) DEFAULT NULL,
    `ban` tinyint(1) NOT NULL DEFAULT 0,
    `ip_address` varchar(45) NOT NULL,
    `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
    `data` blob NOT NULL,
    KEY `ci_sessions_timestamp` (`timestamp`)
);

-- test.
DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `thing` varchar(20) NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

-- Add test data.
INSERT INTO test(thing) VALUES ('Hawk'), ('Tiger'), ('Shark');
INSERT INTO role(role) VALUES ('admin'), ('user');
INSERT INTO user(role, username, password) VALUES ('admin', 'admin', 'admin'), ('user', 'user', 'user');
```

Please open the URL below and check that the login screen is displayed.  

https://<Your host name>

## Command for testing

Batch lock test.  

```sh
CI_ENV=development php public/index.php batch/locktest/run;
```