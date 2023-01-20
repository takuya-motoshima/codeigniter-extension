# codeigniter-extension
You can use extended core classes (controllers, models, views) and utility classes in this package.  
Click [here](CHANGELOG.md) to see the change log.  

There is a sample application in [sample](sample).  
Please use it as a reference for your development.

- [codeigniter-extension](#codeigniter-extension)
  - [Requirements](#requirements)
  - [Getting Started](#getting-started)
  - [Usage](#usage)
  - [Unit testing](#unit-testing)
  - [Author](#author)
  - [License](#license)

## Requirements
- PHP 7.3.0 or later
- Composer
- php-gd
- php-mbstring
- php-xml

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
    If you are using Nginx, copy [nginx.sample.conf](nginx.sample.conf) to "/etc/nginx/conf.d/sample.conf".  
    Restart Nginx.  
    ```sh
    sudo systemctl restart nginx
    ```
1. Build a DB for [create-db.sql](skeleton/create-db.sql) (MySQL or MariaDB).
1. The skeleton uses webpack for front module bundling.  
    The front module is located in ". /client".  
    How to build the front module:  
    ```sh
    cd client
    npm run build
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

## Usage
See [https://codeigniter.com/userguide3/](https://codeigniter.com/userguide3/) for basic usage.  
- About config (application/config/config.php).
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
- Control of accessible URLs.  
    1. Define a controller to be executed when the root URL is accessed.  
        In the example below, the login page is set to open when the root URL is accessed.  

        application/config/routes.php:
        ```php
        $route['default_controller'] = 'users/login';
        ```
    2. Define login session name.  
        application/config/constants.php:
        ```php
        const SESSION_NAME = 'session';
        ```
    3. Create control over which URLs can be accessed depending on the user's login status.  
        At the same time, add env loading and error handling in "pre_system".  

        application/config/hooks.php:
        ```php
        use \X\Annotation\AnnotationReader;
        use \X\Util\Logger;

        $hook['post_controller_constructor'] = function() {
          if (is_cli())
            return;
          $CI =& get_instance();
          $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
          $isLogin = !empty($_SESSION[SESSION_NAME]);
          $currentPath = lcfirst($CI->router->directory ?? '') . lcfirst($CI->router->class) . '/' . $CI->router->method;
          $defaultPath = '/users/index';
          $allowRoles = !empty($meta->allow_role) ? array_map('trim', explode(',', $meta->allow_role)) : null;
          if (!$meta->allow_http)
            throw new \RuntimeException('HTTP access is not allowed');
          else if ($isLogin && !$meta->allow_login)
            redirect($defaultPath);
          else if (!$isLogin && !$meta->allow_logoff)
            redirect('/users/login');
          else if ($isLogin && !empty($allowRoles)) {
            $role = $_SESSION[SESSION_NAME]['role'] ?? '';
            if (!in_array($role, $allowRoles) && $defaultPath !== $currentPath)
              redirect($defaultPath);
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
    4. After this, you will need to create controllers, models, and views, see the sample for details.  
- About Twig Template Engine.  
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
- To extend form validation.  
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

## Unit testing
The unit test consists of the following files.  
- tests/*.php: Test Case.
- phpunit.xml: Test setting fill.
- phpunit-printer.yml: Test result output format.

Run a test.  
```sh
composer test
# PHPUnit Pretty Result Printer 0.32.0 by Codedungeon and contributors.
# PHPUnit 8.5.15 by Sebastian Bergmann and contributors.
# 
# Runtime:       PHP 7.4.21
# Configuration: /var/www/html/codeigniter-extension/phpunit.xml
# 
# 
#  ==> SampleTest                                   PASS  PASS
#  ==> EmailValidationTest                          PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS  PASS
# 
# Time: 40 ms, Memory: 6.00 MB
# 
# OK (30 tests, 30 assertions)
```

## Author
**Takuya Motoshima**

* [github/takuya-motoshima](https://github.com/takuya-motoshima)
* [twitter/TakuyaMotoshima](https://twitter.com/TakuyaMotoshima)
* [facebook/takuya.motoshima.7](https://www.facebook.com/takuya.motoshima.7)

## License
[MIT](LICENSE)