# CodeIgniter Extension

Codeigniter extension package.  
It extends the core classes (controllers, models, views) and adds useful libraries.  

This package installs the offical [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) (version `3.1.*`) with secure folder structure via Composer.

The following must be installed before running this package.  

* PHP 7.3.0 or later
* composer
* git
* php-gd
* php-mbstring
* php-xml

## Changelog

See [CHANGELOG.md](./CHANGELOG.md).

## Examples

The sample application is in "./sampleapp", so please refer to it.

## Getting Started

Create project.  

```sh
composer create-project takuya-motoshima/codeIgniter-extension myapp;
```

Grant log, session, and cache write permissions to the web server.  

```sh
sudo chmod -R 755 ./application/{logs,cache,session};
sudo chown -R nginx:nginx ./application/{logs,cache,session};
```

Web server settings.  
Add the following to /etc/nginx/conf.d/<Your application name>.conf.  

When accessing with the root URL.  
A sample nginx config file can be found in [nginx.sample.conf](./nginx.sample.conf).  

When the domain is the same and the URL is separated. e.g. //<Your server name>/admin

```nginx
location /<Your application name> {
  alias <Your application root directory>/public;
  try_files $uri $uri/ /<Your application name>/index.php;
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

## Usage

See [https://codeigniter.com/](https://codeigniter.com/) for basic usage of Codeigniter.  

### About application config

The basic settings are defined in ./application/config/config.php.  

|Name|Before|After|
|--|--|--|
|base_url||if (!empty($_SERVER['HTTP_HOST'])) $config['base_url'] = '//' . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);|
|enable_hooks|FALSE|TRUE|
|permitted_uri_chars|'a-z 0-9~%.:_\-'|'a-z 0-9~%.:_\-,'|
|sess_save_path|NULL|APPPATH . 'session';|
|cookie_httponly|FALSE|TRUE|
|composer_autoload|FALSE|realpath(APPPATH . '../vendor/autoload.php');|
|index_page|'index.php'|''|

### Access control of action by annotation  

application/config/hooks.php:  

```php
use \X\Annotation\AnnotationReader;

// Add access control to hooks.
$hook['post_controller_constructor'] = function() {
  $ci =& get_instance();

  // Get access from annotations.
  $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);

  // Whether you are logged in.
  $islogin = !empty($_SESSION['user']);

  // Whether it is HTTP access.
  $ishttp = !is_cli();

  // Request URL.
  $requesturl = $ci->router->directory . $ci->router->class . '/' . $ci->router->method;

  // When accessed by HTTP.
  if ($ishttp) {
    // Returns an error if HTTP access is not allowed.
    if (!$accessibility->allow_http) throw new \RuntimeException('HTTP access is not allowed.');

    // When the logged-in user calls a request that only the log-off user can access, redirect to the dashboard.
    // It also redirects to the login page when the log-off user calls a request that only the logged-in user can access.
    if ($islogin && !$accessibility->allow_login) redirect('/dashboard');
    else if (!$islogin && !$accessibility->allow_logoff) redirect('/login');
  } else {
    // When executed with CLI.
  }
};
```

application/ccontrollers/Sample.php:  

```php
use \X\Annotation\Access;
class Sample extends AppController {
  
  /**
   * Only log-off users can access it.
   * 
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function login() {}
  
  /**
   * Only logged-in users can access it..
   * 
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function dashboard() {}
  
  /**
   * It can only be done with the CLI.
   * 
   * @Access(allow_http=false)
   */
  public function batch() {}
}
```

## License

[MIT licensed](./LICENSE.txt)