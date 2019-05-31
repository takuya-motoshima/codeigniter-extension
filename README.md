# CodeIgniterExtension

[![Latest Stable Version](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/v/stable)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![Total Downloads](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/downloads)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![Latest Unstable Version](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/v/unstable)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions) [![License](https://poser.pugx.org/takuya-motoshima/codeigniter-extensions/license)](https://packagist.org/packages/takuya-motoshima/codeigniter-extensions)

This package installs the offical [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) (version `3.1.*`) with secure folder structure via Composer.

You can update CodeIgniter system folder to latest version with one command.

## Folder Structure

```
codeigniter/
├── application/
├── composer.json
├── composer.lock
├── public/
│   ├── .htaccess
│   └── index.php
└── vendor/
    └── codeigniter/
        ├── framework/
        │   └── system/
        ├── facebook/
        │   └── graph-sdk/
        ├── hybridauth/
        │   └── hybridauth/
        ├── symfony/
        │   └── polyfill-mbstring/
        ├── twig/
        │   └── twig/
        └── framework/
```

## Requirements

* PHP 7.0.0 or later
* `composer` command (See [Composer Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx))
* Git
* GD

## Installation procedure 

- Create a project of CodeigniterExtension

```
composer create-project takuya-motoshima/codeIgniter-extension my-project
```

- Grant write permission to the system directory
```
sudo chmod -R 777 ./application/{logs,cache,session};
sudo chown -R nginx:nginx ./application/{logs,cache,session};
```

- Install GD(Required for \X\Util\Image)
```
sudo yum -y install php-gd.x86_64 --enablerepo=remi-php71;
php -m|grep gd;
```

- If the web server is nginx, add the following settings
```
server {
    listen       80;
    server_name  {Your server name};
    charset UTF-8;
    root {Your document root};
    index index.php index.html;
    location /{Your application name} {
        alias {Root directory path of CodeIgniterExtension project}/public;
        try_files $uri $uri/ /{Your application name}/index.php;
        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_index index.php;
            fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
            include fastcgi_params;
            fastcgi_param   SCRIPT_FILENAME $request_filename;
        }
        location ~ .*\.(html?|jpe?g|gif|png|css|js|ico|swf|inc) {
            add_header Access-Control-Allow-Origin *;
            expires 1d;
            access_log off;
        }
    }
}
```

Above command installs `public/.htaccess` to remove `index.php` in your URL. If you don't need it, please remove it.

And it changes `application/config/config.php`:

~~~
$config['base_url'] = '';
↓
if (!empty($_SERVER['HTTP_HOST'])) {$config['base_url'] = "//".$_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);}
~~~

~~~
$config['enable_hooks'] = FALSE;
↓
$config['enable_hooks'] = TRUE;
~~~

~~~
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
↓
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-,';
~~~

~~~
$config['sess_save_path'] = NULL;
↓
$config['sess_save_path'] = APPPATH . 'session';
~~~

~~~
$config['cookie_httponly']  = FALSE;
↓
$config['cookie_httponly']  = TRUE;
~~~

~~~
$config['composer_autoload'] = FALSE;
↓
$config['composer_autoload'] = realpath(APPPATH . '../vendor/autoload.php');
~~~

~~~
$config['index_page'] = 'index.php';
↓
$config['index_page'] = '';
~~~

You must update files manually if files in `application` folder or `index.php` change. Check [CodeIgniter User Guide](http://www.codeigniter.com/user_guide/installation/upgrading.html).

## Third party reference

* [Composer Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* [CodeIgniter](https://github.com/bcit-ci/CodeIgniter)

## Reference of this package

- Access control of action by annotation
```
// application/config/hooks.php
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

// application/ccontrollers/Example.php  
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


## Author
[Takuya Motoshima](https://github.com/takuya-motoshima)