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

Latest 3 changelogs.  

### [3.9.0] - 2021-03-15

* Added a log function that does not output path information.

    ```php
    use \X\Util\Logger;

    Logger::printHidepath('I told you so');
    ```

### [3.8.9] - 2021-02-24

* Added batch exclusive control sample program for file lock and advisory lock to the sample application.
    
    Description of the added file.  

    <table>
      <thead>
        <tr>
          <th>File</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>sampleapp/application/controllers/batch/RunMultipleBatch.php</td>
          <td>An entry point that launches multiple batches at the same time.</td>
        </tr>
        <tr>
          <td>sampleapp/application/controllers/batch/FileLockBatch.php</td>
          <td>Batch with file locking.This is called from RunMultipleBatch.</td>
        </tr>
        <tr>
          <td>sampleapp/application/controllers/batch/AdvisoryLockBatch.php</td>
          <td>Batch with advisory lock.This is called from RunMultipleBatch.</td>
        </tr>
      </tbody>
    </table>

    How to do it.  

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

### [3.8.8] - 2021-02-23

* Organized readme and added batch lock test program.

### [3.8.7] - 2021-02-19

- Added a method to the file helper that returns a file size with units.

    ```php
    use \X\Util\FileHelper;

    FileHelper::humanFilesize('/var/somefile.txt', 0);// 12B
    FileHelper::humanFilesize('/var/somefile.txt', 4);// 1.1498GB
    FileHelper::humanFilesize('/var/somefile.txt', 1);// 117.7MB
    FileHelper::humanFilesize('/var/somefile.txt', 5);// 11.22833TB
    FileHelper::humanFilesize('/var/somefile.txt', 3);// 1.177MB
    FileHelper::humanFilesize('/var/somefile.txt');// 120.56KB
    ```

## Examples

The sample application is in "./sampleapp", so please refer to it.  
Please refer to [sampleapp/README.md](sampleapp/README.md) for how to use the sample application.

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

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Before</th>
      <th>After</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>base_url</td>
      <td></td>
      <td>if (!empty($_SERVER['HTTP_HOST'])) $config['base_url'] = '//' . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);</td>
    </tr>
    <tr>
      <td>enable_hooks</td>
      <td>FALSE</td>
      <td>TRUE</td>
    </tr>
    <tr>
      <td>permitted_uri_chars</td>
      <td>a-z 0-9~%.:_\-</td>
      <td>a-z 0-9~%.:_\-,</td>
    </tr>
    <tr>
      <td>sess_save_path</td>
      <td>NULL</td>
      <td>APPPATH . 'session';</td>
    </tr>
    <tr>
      <td>cookie_httponly</td>
      <td>FALSE</td>
      <td>TRUE</td>
    </tr>
    <tr>
      <td>composer_autoload</td>
      <td>FALSE</td>
      <td>realpath(APPPATH . '../vendor/autoload.php');</td>
    </tr>
    <tr>
      <td>index_page</td>
      <td>index.php</td>
      <td></td>
    </tr>
  </tbody>
</table>

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