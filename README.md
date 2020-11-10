signin.js#  CodeIgniter Extension

A package for efficient development of Codeigniter.  
A simple interface and some general-purpose processing classes have been added to make it easier to use models and libraries.  

This package installs the offical [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) (version `3.1.*`) with secure folder structure via Composer.

You can update CodeIgniter system folder to latest version with one command.  

[![Latest Stable Version](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/v/stable)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![Total Downloads](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/downloads)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![Latest Unstable Version](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/v/unstable)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![License](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/license)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions)

## Release Notes

### 3.6.6 (11/10/2020)

* Add PID to log message(\X\Util\Logger).

### 3.6.5 (11/9/2020)

* Fixed to ignore directory creation error (\X\Util\FileHelper::makeDirectory).

### 3.6.4 (11/6/2020)

* Remove class and function names from the log(\X\Util\Logger).

### 3.6.3 (11/2/2020)

* Changed to be able to specify multiple Amazon SES email destinations in an array.(\X\Util\AmazonSesClient)

    ```php
    use \X\Util\AmazonSesClient;

    $client = new AmazonSesClient([
      'credentials' => [
        'key' => $_ENV['AMAZON_SES_ACCESS_KEY'],
        'secret' => $_ENV['AMAZON_SES_SECRET_KEY']
      ],
      'configuration' => $_ENV['AMAZON_SES_CONFIGURATION'],
      'region' => 'us-west-2',
    ]);

    $client
      ->from('me@example.com')
      ->to([
        'foo@example.jp',
        'bar@example.jp',
      ])
      ->subject('Test email')
      ->message('Hello, World.')
      ->send();
    ```

### 3.6.2 (10/29/2020)

* Fixed OpenSSL encryption/decryption method.

    ```php
    use \X\Util\Cipher;
    
    // Get the initialization vector. This should be changed every time to make it difficult to predict.
    $iv = Cipher::generateInitialVector();
    
    // Plaintext.
    $plaintext = 'Hello, World.';

    // Encrypted password.
    $password = 'password';
    // Encrypt.
    $encrypted = Cipher::encrypt($plaintext, $password, $iv);// UHLY5PckT7Da02e42g==

    // Decrypt.
    $decrypted = Cipher::decrypt($encrypted, $password, $iv);// Hello, World.
    ```

### 3.6.1 (10/23/2020)

* Added IP utility class(\X\Util\IpUtils). And since \X\Util\HttpSecurity has moved to IPUtils, I deleted it.

    ```php
    // Get client ip.
    IpUtils::getClientIpFromXFF();//  202.210.220.78

    // IP range check.
    // 202.210.220.64/28
    IpUtils::inRange('202.210.220.63', '202.210.220.64/28');// false
    IpUtils::inRange('202.210.220.64', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.65', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.78', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.79', '202.210.220.64/28');// true
    IpUtils::inRange('202.210.220.80', '202.210.220.64/28');// false
    
    // 192.168.1.0/24
    IpUtils::inRange('192.168.0.255', '192.168.1.0/24'); // false
    IpUtils::inRange('192.168.1.0', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.1.1', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.1.244', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.1.255', '192.168.1.0/24'); // true
    IpUtils::inRange('192.168.2.0', '192.168.1.0/24'); // false
    
    // 118.238.251.130
    IpUtils::inRange('118.238.251.129', '118.238.251.130'); // false
    IpUtils::inRange('118.238.251.130', '118.238.251.130'); // true
    IpUtils::inRange('118.238.251.131', '118.238.251.130'); // false
    
    // 118.238.251.130/32
    IpUtils::inRange('118.238.251.129', '118.238.251.130/32'); // false
    IpUtils::inRange('118.238.251.130', '118.238.251.130/32'); // true
    IpUtils::inRange('118.238.251.131', '118.238.251.130/32'); // false
    
    // 2001:4860:4860::8888/32
    IpUtils::inRange('2001:4859:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32');// false
    IpUtils::inRange('2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4860:4860:0000:0000:0000:0000:8889', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFE', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32');// true
    IpUtils::inRange('2001:4861:0000:0000:0000:0000:0000:0000', '2001:4860:4860::8888/32');// false
    
    // 2404:7a81:b0a0:9100::/64
    IpUtils::inRange('2404:7A81:B0A0:90FF:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false
    IpUtils::inRange('2404:7A81:B0A0:9100:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:0000:0000:0000:0001', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:A888:5EE2:EA92:B618', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:D03:959E:7F47:9B77', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFE', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFF', '2404:7A81:B0A0:9100::/64');// true
    IpUtils::inRange('2404:7A81:B0A0:9101:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false

    // IPv4 format check.
    IpUtils::isIPv4('234.192.0.2');// true
    IpUtils::isIPv4('234.198.51.100');// true
    IpUtils::isIPv4('234.203.0.113');// true
    IpUtils::isIPv4('0000:0000:0000:0000:0000:ffff:7f00:0001');// false
    IpUtils::isIPv4('::1');// false

    // IPv6 format check.
    IpUtils::isIPv6('234.192.0.2');// false
    IpUtils::isIPv6('234.198.51.100');// false
    IpUtils::isIPv6('234.203.0.113');// false
    IpUtils::isIPv6('0000:0000:0000:0000:0000:ffff:7f00:0001');// true
    IpUtils::isIPv6('::1');// true

    ```

### 3.6.0 (10/20/2020)

* Add a time stamp to the log message output to the CLI(\X\Util\Logger#printWithoutPath).

### 3.5.9 (10/19/2020)

* Added log output method without file path(\X\Util\Logger#printWithoutPath).

### 3.5.8 (10/16/2020)

* Fixed a bug that IP acquisition fails when XFF is empty(\X\Util\HttpSecurity#getIpFromXFF).

### 3.5.7 (10/15/2020)

* Added method to get IP from XFF(\X\Util\HttpSecurity#getIpFromXFF).

### 3.5.5 (6/4/2020)

* Added a method to AA that returns the size of all files in a directory.

    ```php
    // Returns the total size of all files in a directory
    FileHelper::getDirectorySize('/var/log');

    // Returns the total size of all files in multiple directories
    FileHelper::getDirectorySize([ '/var/log/php-fpm' '/var/log/nginx' ]);
    ```

### 3.5.4 (6/4/2020)

* Add encryption key to the parameter of hash conversion method

    ```php
    use \X\Util\Cipher;

    $password = 'password';
    Cipher::encode_sha256('tiger', $password);// c30675022a22cf76c622b7982e8894dd5ac03c4bb2f17ac13a5da01a76acbe6c
    ```

### 3.5.3 (5/20/2020)

* Added a process to log out a user who is logged in with the same ID on another device when logging in

    * config/hooks.php:

        ```php
        use \X\Annotation\AnnotationReader;

        $hook['post_controller_constructor'] = function() {
          isset($_SESSION['user']) ? handlingLoggedIn() : handlingLogOff();
        };

        /**
         * Process for logged-in user
         */
        function handlingLoggedIn() {
          $ci =& get_instance();
          // If it is BAN, call the logoff process
          $ci->load->model('UserService');
          if ($ci->UserService->isBanUser(session_id())) {
            // Sign out
            $ci->UserService->signout();
            // Set ban message display flag
            $ci->load->helper('cookie');
            set_cookie('show_ban_message', true, 10);
            // To logoff processing
            return handlingLogOff();
          }
          // Check if the request URL has access privileges
          $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
          if (!$accessibility->allow_login || ($accessibility->allow_role && $accessibility->allow_role !== $session['role'])) {
            // In case of access prohibition action, redirect to the dashboard page
            redirect('/dashboard');
          }
        }

        /**
         * Process for logoff user
         */
        function handlingLogOff() {
          $ci =& get_instance();
          // Check if the request URL has access privileges
          $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
          if (!$accessibility->allow_logoff) {
            // In case of access prohibition action, redirect to the login page
            redirect('/signin');
          }
        }
        ```

    * models/UserService.php:

        ```php
        class UserService extends \AppModel {

          protected $model = [
            'UserModel',
            'SessionModel'
          ];

          public function signin(string $username, string $password): bool {
            // Find data matching ID and password
            $user = $this->UserModel->getUserByUsernameAndPassword($username, $password);
            if (empty($user)) {
              return false;
            }
            unset($user['password']);
            // Change the BAN flag of other logged-in users to on
            $this->SessionModel->updateSessionBanFlagOn($username, session_id());
            // Store login user data in session
            $_SESSION['user'] = $user;
            return true;
          }

          public function signout() {
            session_destroy();
          }

          public function isBanUser(string $sessionId) {
            return $this->SessionModel->isBanById(session_id());
          }
        }
        ```

    * models/SessionModel.php:

        ```php
        class SessionModel extends \AppModel {

          const TABLE = 'session';

          public function updateSessionBanFlagOn($username, string $id) {
            parent
              ::set('ban', 1)
              ::where('username', $username)
              ::where('id !=', $id)
              ->update();
          }

          public function isBanById(string $id): bool {
            return parent
              ::where('id', $id)
              ::where('ban', 1)
              ::count_all_results() > 0;
          }
        }
        ```

    * controllers/api/User.php

        ```php
        use \X\Annotation\Access;
        use const \X\Constant\HTTP_BAD_REQUEST;
        use const \X\Constant\HTTP_CREATED;
        use const \X\Constant\HTTP_NO_CONTENT;

        class User extends AppController {

          protected $model = [
            'UserService',
            'UserModel',
          ];

          /**
           * @Access(allow_login=false, allow_logoff=true)
           */
          public function signin() {
            try {
              $this->form_validation
                ->set_data($this->input->post())
                ->set_rules('username', 'username', 'required|max_length[30]')
                ->set_rules('password', 'password', 'required|max_length[30]');
              if (!$this->form_validation->run()) {
                return parent::error(print_r($this->form_validation->error_array(), true), HTTP_BAD_REQUEST);
              }
              $result = $this->UserService->signin($this->input->post('username'), $this->input->post('password'));
              parent
                ::set($result)
                ::json();
            } catch (\Throwable $e) {
              parent::error($e->getMessage(), HTTP_BAD_REQUEST);
            }
          }

          /**
           * @Access(allow_login=true, allow_logoff=false)
           */
          public function signout() {
            try {
              $this->UserService->signout();
              redirect('/signin');
            } catch (\Throwable $e) {
              parent::error($e->getMessage(), HTTP_BAD_REQUEST);
            }
          }
        }
        ```

    * public/assets/signin.js

        ```js
        (() => {
          /**
           * Set up login form
           *
           * @return {void}
           */
          function setupLoginForm() {
            const validator = $('#signupForm').validate({
              submitHandler: async (form, event) => {
                event.preventDefault();
                const response = await $.ajax({
                  url: 'api/user/signin',
                  type: 'POST',
                  data: new FormData(form),
                  processData: false,
                  contentType: false
                });
                console.log('response=', response);
                if (!response) {
                  return void validator.showErrors({ username: 'Wrong username or password' });
                }
                location.href = '/';
              }
            });
          }

          // Set up login form
          setupLoginForm();

          // Display BAN message
          if (Cookies.get('show_ban_message')) {
            Cookies.remove('show_ban_message')
            alert('Logged out because it was logged in on another terminal.');
          }
        })();
        ````

### 3.5.0 (5/19/2020)

* Fixed a bug that DB class does not inherit \X\Database\QueryBuilder when making session DB

### 3.4.8 (4/28/2020)

* Make the IP range check method of "\X\Util\HttpSecurity" class do correct check when subnet mask is 32.

    ```php
    use \X\Util\HttpSecurity;

    HttpSecurity::isAllowIp('202.210.220.64',   '202.210.220.64/28');// false
    HttpSecurity::isAllowIp('202.210.220.65',   '202.210.220.64/28');// true
    HttpSecurity::isAllowIp('202.210.220.66',   '202.210.220.64/28');// true
    HttpSecurity::isAllowIp('202.210.220.78',   '202.210.220.64/28');// true
    HttpSecurity::isAllowIp('202.210.220.79',   '202.210.220.64/28');// false
    HttpSecurity::isAllowIp('202.210.220.80',   '202.210.220.64/28');// false
    HttpSecurity::isAllowIp('192.168.0.0',      '192.168.1.0/24');// false
    HttpSecurity::isAllowIp('192.168.1.0',      '192.168.1.0/24');// false
    HttpSecurity::isAllowIp('192.168.1.1',      '192.168.1.0/24');// true
    HttpSecurity::isAllowIp('192.168.1.254',    '192.168.1.0/24');// true
    HttpSecurity::isAllowIp('192.168.1.255',    '192.168.1.0/24');// false
    HttpSecurity::isAllowIp('118.238.251.130',  '118.238.251.130');// true
    HttpSecurity::isAllowIp('118.238.251.131',  '118.238.251.130');// false
    HttpSecurity::isAllowIp('118.238.251.130',  '118.238.251.130/32');// true
    HttpSecurity::isAllowIp('118.238.251.131',  '118.238.251.130/32');// false
    ```

### 3.4.7 (4/27/2020)

* Added feature to face detector to find multiple faces from collection

    ```php
    use \X\Rekognition\Client;
    $client = new Client('AWS_REKOGNITION_KEY', 'AWS_REKOGNITION_SECRET');

    // Find a single face in a collection
    $collectionId = '8e5e6f4e99f380b';
    $faceImage = 'data:image/png;base64,...';
    $threshold = 80;
    $detection = $client->getFaceFromCollectionByImage($collectionId, $faceImage, $threshold);
    // Array
    // (
    //     [faceId] => b3fcb4ed-5891-4bc3-bb1c-6b3f90f159d1
    //     [similarity] => 99.7
    // )

    // Find multiple faces in a collection
    $collectionId = '8e5e6f4e99f380b';
    $faceImage = 'data:image/png;base64,...';
    $threshold = 80;
    $maxFaces = 4096;
    $detections = $client->getMultipleFacesFromCollectionByImage($collectionId, $faceImage, $threshold, $maxFaces);
    // Array
    // (
    //     [0] => Array
    //         (
    //             [faceId] => e12c40d2-445d-4b12-bfad-db46a2f611dc
    //             [similarity] => 99.7
    //         )
    //
    //     [1] => Array
    //         (
    //             [faceId] => b3fcb4ed-5891-4bc3-bb1c-6b3f90f159d1
    //             [similarity] => 99.3
    //         )
    //
    // )
    ```

### 3.4.6 (4/23/2020)

* Added a feature to add arbitrary columns to the session table

    Set the columns you want to add to the session table in "application/confi /config.php".
    The example adds the username column to the session table.

    <p class="alert">Be sure to allow NULL for your own extra columns. This is because the session created when you are not logged in has no extra column values.</p>

    ```php
    // Session table additional column.
    // A session field with the same name as the additional column name is saved in the table.
    $config['sess_table_additional_columns'] = ['username'];
    ```

    Create a session table.

    ```sql
    CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(128) NOT NULL,
        `username` varchar(255) DEFAULT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
    );
    ```

    Create a session database class (application/libraries/Session/drivers/AppSession_database_driver.php) in your application.

    ```php
    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    use X\Library\SessionDatabaseDriver;

    class AppSession_database_driver extends SessionDatabaseDriver {}
    ```

    The user name of the logged-in user will be added to the session table.

    ```sql
    SELECT * FROM session WHERE username='admin'\G;

    *************************** 1. row ***************************
            id: 78g8c230pe8onb93jkpbbkatcii3h7ss
      username: admin
    ip_address: 172.31.38.40
     timestamp: 1587646280
          data: ...
    1 rows in set (0.000 sec)
    ```

### 3.4.5 (4/10/2020)

* Changed to return an empty string when there is no key value to get from the config with "\X\Utils\Loader::config()".

### 3.4.2 (3/16/2020)

* Added setting of template cache in application config (application/config/config.php).

    You can configure the template cache in "application/config/config.php".

    ```php
    /*
    |--------------------------------------------------------------------------
    | Template settings
    |--------------------------------------------------------------------------
    |
    | 'csrf_token_name' = Directory path to store template cache. Set FALSE when not caching. The default is FALSE.
    */
    $config['cache_templates'] = false;
    ```

### 3.3.9 (3/16/2020)

* Added client class that summarizes face detection processing. Remove old face detection class.

    ```php
    use \X\Rekognition\Client;
    $client = new Client('AWS_REKOGNITION_KEY', 'AWS_REKOGNITION_SECRET');

    // Create new face collection
    $collectionId = $client->generateCollectionId(FCPATH . 'protected');
    $client->addCollection($collectionId);

    // Add a face photo in data URL format to the collection
    $faceImage = 'data:image/png;base64,...';
    $faceId = $client->addFaceToCollection($collectionId, $faceImage);

    // You can also add to the collection from the photo file path.
    $faceImageFile = './face.png';
    $faceId = $client->addFaceToCollection($collectionId, $faceImageFile);
    ```

### 3.3.8 (3/14/2020)

* Added insert_on_duplicate_update.

    ```php
    // Here is an example of insert_on_duplicate_update.
    $SampleModel
    ->set([
    'key' => '1',
    'title' => 'My title',
    'name' => 'My Name'
    ])
    ->insert_on_duplicate_update();

    // You can also
    $SampleModel
      ->set('key', '1')
      ->set('title', 'My title')
      ->set('name', 'My Name')
      ->insert_on_duplicate_update();
    ```

* Added insert_on_duplicate_update_batch.

    ```php
    // Here is an example of insert_on_duplicate_update_batch
    $SampleModel
      ->set_insert_batch([
        [
          'key' => '1',
          'title' => 'My title',
          'name' => 'My Name'
        ],
        [
          'key' => '2',
          'title' => 'Another title',
          'name' => 'Another Name'
        ]
      ])
      ->insert_on_duplicate_update_batch();
    ```

## Requirements

* PHP 7.0.0 or later
* composer (See [Composer Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx))
* git
* php-gd
* php-mbstring
* php-xml
* php-mcrypt

## Getting Started

1. Create project.

    ```sh
    composer create-project takuya-motoshima/codeIgniter-extension myapp
    ```

1. Grant log, session, and cache write permissions to the web server.

    ```sh
    sudo chmod -R 755 ./application/{logs,cache,session};
    sudo chown -R nginx:nginx ./application/{logs,cache,session};
    ```

1. Web server settings.

    1. Add the following to /etc/nginx/conf.d/{Your application name}.conf.  

        When accessing with the root URL. e.g. //{Your server name} :  

        ```nginx
        server {
            listen 80;
            server_name  {Your server name};
            charset UTF-8;
            root {Your application root directory}/public;
            index index.php index.html;
            access_log  /var/log/nginx/{Your application name}.access.log;
            error_log  /var/log/nginx/{Your application name}.error.log  warn;

            # Execute php with php file and HTML file using FastCGI.This is a setting that is not related to codeigniter
            location ~\.(php|html)$ {
                fastcgi_pass unix:/run/php-fpm/www.sock;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
                include        fastcgi_params;
            }

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
                # fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
            }
        }
        ```

        When the domain is the same and the URL is separated. e.g. //{Your server name}/admin :  

            ```nginx
            location /{Your application name} {
                alias {Your application root directory}/public;
                try_files $uri $uri/ /{Your application name}/index.php;
                location ~ \.php$ {
                    fastcgi_split_path_info ^(.+\.php)(/.+)$;
                    fastcgi_index index.php;
                    fastcgi_pass unix:/run/php-fpm/www.sock;
                    include fastcgi_params;
                    #fastcgi_param CI_ENV development;
                    fastcgi_param SCRIPT_FILENAME $request_filename;
                }
            }
            ```

    1. Restart nginx to reflect the setting.  

        ```sh
        sudo systemctl restart nginx;
        ```

1. Application settings.

    Open config.  

    ```sh
    vim ./application/config/config.php;
    ```

    Edit content:  

    |Name|Before|After|
    |--|--|--|
    |base_url||if (!empty($_SERVER['HTTP_HOST'])) $config['base_url'] = '//' . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);|
    |enable_hooks|FALSE|TRUE|
    |permitted_uri_chars|'a-z 0-9~%.:_\-'|'a-z 0-9~%.:_\-,'|
    |sess_save_path|NULL|APPPATH . 'session';|
    |cookie_httponly|FALSE|TRUE|
    |composer_autoload|FALSE|realpath(APPPATH . '../vendor/autoload.php');|
    |index_page|'index.php'|''|

    ## Project structure.

    ```sh
    .
    |-- src
    |   `-- X
    |       |-- Annotation
    |       |   |-- Access.php
    |       |   `-- AnnotationReader.php
    |       |-- Composer
    |       |   `-- Installer.php
    |       |-- Constant
    |       |   |-- Environment.php
    |       |   `-- HttpStatus.php
    |       |-- Controller
    |       |   `-- Controller.php
    |       |-- Data
    |       |   `-- address.json
    |       |-- Database
    |       |   |-- DB.php
    |       |   |-- Driver
    |       |   |   |-- Cubrid
    |       |   |   |-- Ibase
    |       |   |   |-- Mssql
    |       |   |   |-- Mysql
    |       |   |   |-- Mysqli
    |       |   |   |-- Oci8
    |       |   |   |-- Odbc
    |       |   |   |-- Pdo
    |       |   |   |-- Postgre
    |       |   |   |-- Sqlite
    |       |   |   |-- Sqlite3
    |       |   |   `-- Sqlsrv
    |       |   |-- QueryBuilder.php
    |       |   `-- Result.php
    |       |-- Exception
    |       |   |-- AccessDeniedException.php
    |       |   `-- RestClientException.php
    |       |-- Hook
    |       |   `-- Authenticate.php
    |       |-- Library
    |       |   |-- Input.php
    |       |   `-- Router.php
    |       |-- Model
    |       |   |-- AddressModel.php
    |       |   |-- Model.php
    |       |   |-- SessionModelInterface.php
    |       |   `-- SessionModel.php
    |       `-- Util
    |           |-- AmazonRekognitionClient.php
    |           |-- AmazonSesClient.php
    |           |-- ArrayHelper.php
    |           |-- Cipher.php
    |           |-- CsvHelper.php
    |           |-- DateHelper.php
    |           |-- EMail.php
    |           |-- FileHelper.php
    |           |-- HtmlHelper.php
    |           |-- HttpResponse.php
    |           |-- ImageHelper.php
    |           |-- Iterator.php
    |           |-- Loader.php
    |           |-- Logger.php
    |           |-- RestClient.php
    |           |-- SessionHelper.php
    |           |-- StringHelper.php
    |           |-- Template.php
    |           `-- UrlHelper.php
    |-- composer.json
    |-- composer.json.dist
    |-- composer.lock
    |-- composer.phar
    |-- core.dist
    |   |-- AppController.php
    |   |-- AppInput.php
    |   `-- AppModel.php
    |-- dot.gitattributes.dist
    |-- dot.gitignore.dist
    |-- dot.htaccess
    |-- LICENSE.md
    |-- package.json.dist
    |-- README.md
    |-- script.js
    |-- examples/
    |-- views.dist
    |   |-- common
    |   |   `-- base.html
    |   `-- index.html
    `-- webpack.config.js.dist
    ```

## Reference

- [CodeIgniter Web Framework](https://codeigniter.com/)  
- Access control of action by annotation  

    application/config/hooks.php:  

    ```php
    use \X\Annotation\AnnotationReader;  
    $hook['post_controller_constructor'] = function() {  
     $ci =& get_instance();
     $accessControl = AnnotationReader::getMethodAccessControl($ci->router->class, $ci->router->method);
     $loggedin = !empty($_SESSION['user']);
     if ($loggedin && !$accessControl->allowLoggedin) {
       // In case of an action that the logged-in user can not access
       redirect('/dashboard');
     } else if (!$loggedin && !$accessControl->allowLoggedoff) {
       // In case of an action that can not be accessed by the user who is logging off
       redirect('/login');
     }
    };
    ```

    application/ccontrollers/Example.php:  

    ```php
    use \X\Annotation\AccessControl;
    class Example extends AppController
    {
     /**
      * @AccessControl(allowLoggedin=false, allowLoggedoff=true)
      */
     public function login() {}

     /**
      * @AccessControl(allowLoggedin=true, allowLoggedoff=false)
      */
     public function dashboard() {}
    }
    ```

## License
[MIT](LICENSE.txt)

## Author
- Twitter: [@TakuyaMotoshima](https://twitter.com/taaaaaaakuya)
- Github: [TakuyaMotoshima](https://github.com/takuya-motoshima)
mail to: development.takuyamotoshima@gmail.com
