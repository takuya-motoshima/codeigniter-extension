#  CodeIgniter Extension

A package for efficient development of Codeigniter.  
A simple interface and some general-purpose processing classes have been added to make it easier to use models and libraries.  

This package installs the offical [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) (version `3.1.*`) with secure folder structure via Composer.

You can update CodeIgniter system folder to latest version with one command.  

[![Latest Stable Version](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/v/stable)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![Total Downloads](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/downloads)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![Latest Unstable Version](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/v/unstable)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![License](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/license)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions)

## Release Notes

### 3.4.6 (April 23, 2020)

* Added a feature to add arbitrary columns to the session table

    Set the columns you want to add to the session table in "application/confi /config.php".
    The example adds the username column to the session table.

    ```PHP
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

### 3.4.5 (April 10, 2020)

* Changed to return an empty string when there is no key value to get from the config with "\X\Utils\Loader::config()".

### 3.4.2 (March 16, 2020)

* Added setting of template cache in application config (application/config/config.php).

    You can configure the template cache in "application/config/config.php".

    ```PHP
    /*
    |--------------------------------------------------------------------------
    | Template settings
    |--------------------------------------------------------------------------
    |
    | 'csrf_token_name' = Directory path to store template cache. Set FALSE when not caching. The default is FALSE.
    */
    $config['cache_templates'] = false;
    ```

### v3.3.9 (March 16, 2020)

* Added client class that summarizes face detection processing. Remove old face detection class.

    ```PHP
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

### v3.3.8 (March 14, 2020)

* Added insert_on_duplicate_update.

    ```PHP
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

    ```PHP
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
