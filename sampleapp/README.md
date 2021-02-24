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
Connect to MariaDB and execute the following command to build the DB.  

```sh
cd /var/www/html/sampleapp;
mysql -u root;
SOURCE ./ddl.sql;
```
Please open the URL below and check that the login screen is displayed.  

https://<Your host name>

## Command for testing

Run a batch that prohibits multiple launches using file locks.  

```sh
cd /var/www/html/sampleapp;
CI_ENV=development php public/index.php batch/runMultipleBatch/run/filelock;
```

Run a batch that prohibits multiple launches using advisory locks.  

```sh
cd /var/www/html/sampleapp;
CI_ENV=development php public/index.php batch/runMultipleBatch/run/advisorylock;
```