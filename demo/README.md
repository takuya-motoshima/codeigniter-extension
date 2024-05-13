# Sample application

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
    If you are using Nginx, copy [nginx.sample.conf](../nginx.sample.conf) to "/etc/nginx/conf.d/sample.conf".  

    Restart Nginx.  
    ```sh
    sudo systemctl restart nginx
    ```
1. Build a DB for [init.sql](init.sql) (MySQL or MariaDB).
1. The skeleton uses webpack for front module bundling.  
    The front module is located in ". /client".  
    How to build the front module:  
    ```sh
    cd client
    npm run build:dev
    ```
1. Open "http://{public IP of the server}:3000/" in a browser and the following screen will appear.  
    **NOTE**: You can log in with the username "robin@example.com" and password "password".  
    <p align="left">
      <img alt="sign-in.png" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/sign-in.png" width="45%">
      <img alt="list-of-users.png" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/list-of-users.png" width="45%">
    </p>
    <p align="left">
      <img alt="update-user.png" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/update-user.png" width="45%">
      <img alt="personal-settings.png" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/personal-settings.png" width="45%">
    </p>
    <p align="left">
      <img alt="page-not-found.png" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/page-not-found.png" width="45%">
    </p>