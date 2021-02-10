# CodeIgniter Extension

Codeigniter extension package.  
It extends the core classes (controllers, models, views) and adds useful libraries.  

This package installs the offical [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) (version `3.1.*`) with secure folder structure via Composer.

## Changelog

See [CHANGELOG.md](./CHANGELOG.md).

## Requirements

* PHP 7.3.0 or later
* composer
* git
* php-gd
* php-mbstring
* php-xml

## Getting Started

### Create project.

```sh
composer create-project takuya-motoshima/codeIgniter-extension myapp
```

### Grant log, session, and cache write permissions to the web server.

```sh
sudo chmod -R 755 ./application/{logs,cache,session};
sudo chown -R nginx:nginx ./application/{logs,cache,session};
```

### Web server settings.

Add the following to /etc/nginx/conf.d/{Your application name}.conf.  

When accessing with the root URL.  
A sample nginx config file can be found in [nginx.sample.conf](./nginx.sample.conf).  

When the domain is the same and the URL is separated. e.g. //{Your server name}/admin

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

Restart nginx to reflect the setting.  

```sh
sudo systemctl restart nginx;
```

### Application settings.

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

[MIT licensed](./LICENSE.txt)