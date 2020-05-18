# Get started with CodeIgniter Extension

1. If the web server is Nginx, create a new setting with the following contents.

    ```
    server {
        listen       80;
        server_name  {Your host name};
        ssi_last_modified on;
        ssi on;
        charset UTF-8;
        root {Application root directory}/public;
        index index.php index.html;
        access_log  /var/log/nginx/{Your application name}.access.log;
        error_log  /var/log/nginx/{Your application name}.error.log  warn;
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

    After saving the settings, restart nginx.

    ```sh
    sudo systemctl restart nginx;
    ```

1. Create a database

    ```sql

    -- Add DB
    CREATE SCHEMA IF NOT EXISTS `codeigniter_extension_example` DEFAULT CHARACTER SET utf8mb4;
    use codeigniter_extension_example;

    -- Add login role table
    DROP TABLE IF EXISTS `role`;
    CREATE TABLE `role` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `role` enum('admin','user') NOT NULL,
        `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `ukRole1` (`role`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Add login user table
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

    -- Add login session table
    DROP TABLE IF EXISTS `session`;
    CREATE TABLE IF NOT EXISTS `session` (
        `id` varchar(128) NOT NULL,
        -- `id` varchar(40) NOT NULL,
        `username` varchar(255) DEFAULT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
    );

    -- Add test table
    DROP TABLE IF EXISTS `test`;
    CREATE TABLE IF NOT EXISTS `test` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `thing` varchar(20) NOT NULL,
        `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    );

    -- Add login role record
    INSERT INTO role(role) VALUES ('admin'), ('user');

    -- Add login user record
    INSERT INTO user(role, username, password) VALUES ('admin', 'admin', 'admin'), ('user', 'user', 'user');
    ```

1.  Please access "https: // {Your host name}" from the browser and confirm.
