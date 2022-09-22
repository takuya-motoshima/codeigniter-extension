# sample
Sample applications and how to use them.

## Getting Started
1. Install dependent packages.
    ```sh
    composer install
    ```
1. Create an ".env" file.
    **NOTE**: The ".env" file is loaded by the process in "application/config/hooks.php".
    ```sh
    cp -a .env.sample .env
    ```
1. Grant write permission to logs, cache, session to WEB server.  
    ```sh
    sudo chmod -R 755 public/upload application/{logs,cache,session}
    sudo chown -R nginx:nginx public/upload application/{logs,cache,session}
    ```
1. Set up a web server (nginx).  
    If you are using Nginx, copy [nginx.sample.conf](../nginx.sample.conf) to "/etc/nginx/conf.d/Your application name.conf".  

    Restart Nginx.  
    ```sh
    sudo systemctl restart nginx
    ```
1. Build a DB for [sampledb.sql](sampledb.sql) (MySQL or MariaDB).
1. Install client (JS and CSS) dependency packages.
    ```sh
    cd client
    npm run build:dev
    ```
If the WEB server settings have been completed, the login page will be displayed from the following URL.  
https:\/\/your hostname