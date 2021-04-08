# codeigniter-extension

You can use extended core classes (controllers, models, views) and utility classes in this package.  
This application requires the following packages.  
* PHP 7.3.0 or later
* Composer
* php-gd
* php-mbstring
* php-xml

## Changelog

See [CHANGELOG.md](./CHANGELOG.md).

## Examples

![screencap.jpg](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/documents/screencap.jpg)

There is a sample application in [./sample](./sample).  
Please use it as a reference for your development.

## Getting Started

Create project.  

```sh
composer create-project takuya-motoshima/codeIgniter-extension myapp;
```

Grant write permission to logs, cache, session to WEB server.  

```sh
sudo chmod -R 755 ./application/{logs,cache,session};
sudo chown -R nginx:nginx ./application/{logs,cache,session};
```

If you are using Nginx, copy [nginx.sample.conf](./nginx.sample.conf) to "/etc/nginx/conf.d/<Your application name> .conf".  
You can start the application immediately.  

Restart Nginx.  

```sh
sudo systemctl restart nginx;
```

That's all for the settings.

## Usage

See [https://codeigniter.com/](https://codeigniter.com/) for basic usage.  

### About config

application/config/config.php:  

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

### Access control with annotations

The following is an example of access control using annotations.  

application/config/constants.php:  

```php
// Login session name.
const SESSION_NAME = 'session';
```

application/config/hooks.php:  

```php
use \X\Annotation\AnnotationReader;

// post_controller_constructor callback.
$hook['post_controller_constructor'] = function() {
  $ci =& get_instance();

  // Get access from annotations.
  $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);

  // Whether you are logged in.
  $islogin = !empty($_SESSION[SESSION_NAME]);

  // Whether it is HTTP access.
  $ishttp = !is_cli();

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
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function login() {}
  
  /**
   * Only logged-in users can access it..
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function dashboard() {}
  
  /**
   * It can only be done with the CLI.
   * @Access(allow_http=false)
   */
  public function batch() {}
}
```

## License

[MIT licensed](./LICENSE.txt)