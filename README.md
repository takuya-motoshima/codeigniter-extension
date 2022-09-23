# codeigniter-extension
You can use extended core classes (controllers, models, views) and utility classes in this package.  

- [codeigniter-extension](#codeigniter-extension)
  - [Requirements](#requirements)
  - [Changelog](#changelog)
  - [Screenshots of the skeleton and samples created by create-project](#screenshots-of-the-skeleton-and-samples-created-by-create-project)
  - [Getting Started](#getting-started)
  - [Usage](#usage)
    - [About config (application/config/config.php)](#about-config-applicationconfigconfigphp)
    - [Control of accessible URLs](#control-of-accessible-urls)
    - [About Twig Template Engine.](#about-twig-template-engine)
    - [To extend form validation.](#to-extend-form-validation)
  - [Author](#author)
  - [License](#license)

## Requirements
- PHP 7.3.0 or later
- Composer
- php-gd
- php-mbstring
- php-xml

## Changelog
See [CHANGELOG.md](./CHANGELOG.md).

## Screenshots of the skeleton and samples created by create-project
There is a sample application in [./sample](./sample).  
Please use it as a reference for your development.

![sign-in.png](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/sign-in.png)
![list-of-users.png](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/list-of-users.png)
![update-user.png](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/update-user.png)
![personal-settings.png](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/personal-settings.png)
![page-not-found.png](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/page-not-found.png)
<!-- ![edit-personal-settings.png](https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/edit-personal-settings.png) -->

## Getting Started
1. Create project.  
    ```sh
    composer create-project takuya-motoshima/codeIgniter-extension myapp
    ```
1. Grant write permission to logs, cache, session to WEB server.  
    ```sh
    sudo chmod -R 755 public/upload application/{logs,cache,session}
    sudo chown -R nginx:nginx public/upload application/{logs,cache,session}
    ```
1. Set up a web server (nginx).  
    If you are using Nginx, copy [nginx.sample.conf](./nginx.sample.conf) to "/etc/nginx/conf.d/Your application name.conf".  

    Restart Nginx.  
    ```sh
    sudo systemctl restart nginx
    ```
    That's all for the settings.
1. Build a DB for [skeletondb.sql](skeletondb.sql) (MySQL or MariaDB).
1. **NOTE**: The skeleton uses webpack for front module bundling.  
    The front module is located in ". /client".  

    How to build the front module:  
    ```sh
    cd client
    npm run build
    ```

## Usage
See [https://codeigniter.com/](https://codeigniter.com/) for basic usage.  

### About config (application/config/config.php)
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

### Control of accessible URLs
1. Define a controller to be executed when the root URL is accessed.  
    In the example below, the login page is set to open when the root URL is accessed.  

    application/config/routes.php:
    ```php
    $route['default_controller'] = 'users/login';
    ```
1. Define login session name.  
    application/config/constants.php:
    ```php
    const SESSION_NAME = 'session';
    ```
1. Create control over which URLs can be accessed depending on the user's login status.  
    At the same time, add env loading and error handling in "pre_system".  

    application/config/hooks.php:
    ```php
    use \X\Annotation\AnnotationReader;
    use \X\Util\Logger;

    $hook['post_controller_constructor'] = function() {
      $ci =& get_instance();
      $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
      $isLogin = !empty($_SESSION[SESSION_NAME]);
      $currentPath = lcfirst($ci->router->directory ?? '') . lcfirst($ci->router->class) . '/' . $ci->router->method;
      $defaultPath = '/users/index';
      $allowRoles = !empty($accessibility->allow_role) ? array_map('trim', explode(',', $accessibility->allow_role)) : null;
      if (!is_cli()) {
        if (!$accessibility->allow_http)
          throw new \RuntimeException('HTTP access is not allowed');
        else if ($isLogin && !$accessibility->allow_login)
          redirect($defaultPath);
        else if (!$isLogin && !$accessibility->allow_logoff)
          redirect('/users/login');
        else if ($isLogin && !empty($allowRoles)) {
          $role = $_SESSION[SESSION_NAME]['role'] ?? 'undefined';
          if (!in_array($role, $allowRoles) && $defaultPath !== $currentPath)
            redirect($defaultPath);
        }
      }
    };

    $hook['pre_system'] = function () {
      $dotenv = Dotenv\Dotenv::createImmutable(ENV_DIR);
      $dotenv->load();
      set_exception_handler(function ($e) {
        Logger::error($e);
        show_error($e->getMessage(), 500);
      });
    };
    ```
1. After this, you will need to create controllers, models, and views, see the sample for details.  

### About Twig Template Engine.
This extension package uses the Twig template.  
See [here](https://twig.symfony.com/doc/3.x/) for how to use Twig.  

In addition, the session of the logged-in user is automatically set in the template variable.  
This is useful, for example, when displaying the login username on the screen. 

PHP: 
```php
$_SESSION['user'] = ['name' => 'John Smith'];
```

HTML: 
```html
{% if session.user is not empty %}
  Hello {{session.user.name}}!
{% endif %}
  Who is it?
{% else %}
```

### To extend form validation.
You can create a new validation rule by creating "application/libraries/AppForm_validation.php" as follows and adding a validation method.
```php
use X\Library\FormValidation;

class AppForm_validation extends FormValidation {
  public function is_numeric(string $input): bool {
    if (!is_numeric($input)) {
      $this->set_message('is_numeric', 'Please enter a numerical value');
      return false;
    }
    return true;
  }
}
```

The following extended validations are available in the CodeIgniter extension from the start.  
<table>
  <thead>
    <tr>
      <th>Rule</th>
      <th>Parameter</th>
      <th>Description</th>
      <th>Example</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>datetime</td>
      <td>Yes</td>
      <td>If the value is other than a date, FALSE is returned..</td>
      <td>datetime[Y-m-d H:i:s]</td>
    </tr>
    <tr>
      <td>hostname</td>
      <td>No</td>
      <td>If the value is other than the host name, FALSE is returned.</td>
      <td></td>
    </tr>
    <tr>
      <td>ipaddress</td>
      <td>No</td>
      <td>If the value is other than an IP address, FALSE is returned.</td>
      <td></td>
    </tr>
    <tr>
      <td>hostname_or_ipaddress</td>
      <td>No</td>
      <td>If the value is other than a host name or IP address, FALSE is returned.</td>
      <td></td>
    </tr>
    <tr>
      <td>unix_username</td>
      <td>No</td>
      <td>If the value is other than a Unix username, FALSE is returned.</td>
      <td></td>
    </tr>
    <tr>
      <td>port</td>
      <td>No</td>
      <td>If the value is other than a port number, FALSE is returned.</td>
      <td></td>
    </tr>
    <tr>
      <td>email</td>
      <td>No</td>
      <td>If the value is other than the email suggested in HTML5, FALSE will be returned.<br><a href="https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address">https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address</a></td>
      <td></td>
    </tr>
  </tbody>
</table>

## Author
**Takuya Motoshima**

* [github/takuya-motoshima](https://github.com/takuya-motoshima)
* [twitter/TakuyaMotoshima](https://twitter.com/TakuyaMotoshima)
* [facebook/takuya.motoshima.7](https://www.facebook.com/takuya.motoshima.7)

## License
[MIT](LICENSE)