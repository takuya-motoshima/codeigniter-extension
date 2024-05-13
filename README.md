# codeigniter-extension
You can use extended core classes (controllers, models, views) and utility classes in this package.  
Click [here](CHANGELOG.md) to see the change log.  

## API Documentation
[https://takuya-motoshima.github.io/codeigniter-extension/](https://takuya-motoshima.github.io/codeigniter-extension/)

## Demonstration
There is a demo application in [demo/](demo/). Please use it as a reference for your development.

## Requirements
- PHP 7.3.0 or later
- Composer
- php-gd
- php-mbstring
- php-xml
- php-imagick  
    The method to extract the first frame from a GIF (`extractFirstFrameOfGif`) in the `\X\Util\ImageHelper` class requires ImageMagick.  
    To use this method, install ImageMagick and php-imagick.  

    - For Amazon LInux 2 OS:
        ```sh
        sudo yum -y install ImageMagick php-imagick
        ```
    - For Amazon LInux 2023 OS:
        1. Install ImageMagic and PECL.
            ```sh
            sudo dnf -y install ImageMagick ImageMagick-devel php-pear.noarch
            ```
        1. Install imagick with PECL.
            ```sh
            sudo pecl install imagick
            ```
        1. Add `imagick.so` link in `/etc/php.ini`.
            ```sh
            extension=imagick.so
            ```
        1. Restart `php-fpm` and `nginx`.
            ```sh
            sudo systemctl restart nginx
            sudo systemctl restart php-fpm
            ```

## Getting Started
1. Create project.
    ```sh
    composer create-project takuya-motoshima/codeigniter-extension myapp
    ```
1. Grant write permission to logs, cache, session to WEB server.
    ```sh
    sudo chmod -R 755 public/upload application/{logs,cache,session}
    sudo chown -R nginx:nginx public/upload application/{logs,cache,session}
    ```
1. Set up a web server (nginx).  
    If you are using Nginx, copy [nginx.sample.conf](nginx.sample.conf) to `/etc/nginx/conf.d/sample.conf`.  
    Restart Nginx.  
    ```sh
    sudo systemctl restart nginx
    ```
1. Build a DB for [init.sql](skeleton/init.sql) (MySQL or MariaDB).
1. The skeleton uses webpack for front module bundling.  
    The front module is located in `./client`.  
    How to build the front module:  
    ```sh
    cd client
    npm run build
    ```
1. Open `http://{public IP of the server}:3000/` in a browser and the following screen will appear.  
    **NOTE**: You can log in with the username `robin@example.com` and password `password`.  
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
- About config (`application/config/config.php`).
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
    1. Define login session name.  
        application/config/constants.php:
        ```php
        const SESSION_NAME = 'session';
        ```
    1. Create control over which URLs can be accessed depending on the user's login status.  
        At the same time, add env loading and error handling in `pre_system`.  

        application/config/hooks.php:
        ```php
        use \X\Annotation\AnnotationReader;
        use \X\Util\Logger;

        $hook['post_controller_constructor'] = function() {
          if (is_cli())
            return;
          $CI =& get_instance();
          $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
          $loggedin = !empty($_SESSION[SESSION_NAME]);
          $current = lcfirst($CI->router->directory ?? '') . lcfirst($CI->router->class) . '/' . $CI->router->method;
          $default = '/users/index';
          $allowRoles = !empty($meta->allow_role) ? array_map('trim', explode(',', $meta->allow_role)) : null;
          if (!$meta->allow_http)
            throw new \RuntimeException('HTTP access is not allowed');
          else if ($loggedin && !$meta->allow_login)
            redirect($default);
          else if (!$loggedin && !$meta->allow_logoff)
            redirect('/users/login');
          else if ($loggedin && !empty($allowRoles)) {
            $role = $_SESSION[SESSION_NAME]['role'] ?? '';
            if (!in_array($role, $allowRoles) && $default !== $current)
              redirect($default);
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
    1. After this, you will need to create controllers, models, and views, see the demo for details.  
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

## Testing
The unit test consists of the following files.  
- __tests__/*.php: Test Case.
- phpunit.xml: Test setting fill.
- phpunit-printer.yml: Test result output format.

```sh
composer test
```

## PHPDoc
Generate PHPDoc in docs/.
```sh
#wget https://phpdoc.org/phpDocumentor.phar
#chmod +x phpDocumentor.phar
php phpDocumentor.phar run -d src/ --ignore vendor --ignore src/X/Database/Driver/ -t docs/
```

## Author
**Takuya Motoshima**

* [github/takuya-motoshima](https://github.com/takuya-motoshima)
* [twitter/TakuyaMotoshima](https://twitter.com/TakuyaMotoshima)
* [facebook/takuya.motoshima.7](https://www.facebook.com/takuya.motoshima.7)

## License
[MIT](LICENSE)