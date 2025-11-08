# CodeIgniter Extension

[日本語](README_ja.md) | [Changelog](CHANGELOG.md) | [変更履歴](CHANGELOG_ja.md)

An enhanced CodeIgniter 3 package providing extended core classes (controllers, models, views) and utility classes.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Usage Examples](#usage-examples)
- [Testing](#testing)
- [Documentation](#documentation)
- [License](#license)

## Features

### Core Extensions
- **Enhanced Controllers** - JSON response, template rendering, access control
- **Advanced Models** - Query caching, batch operations, helper methods
- **Enhanced Router** - Annotation-based access control

### Utility Classes
- **Image Processing** - Resize, crop, format conversion, GIF frame extraction, PDF to image
- **Video Processing** - Video file manipulation and conversion
- **File Operations** - Advanced file and directory operations with locking
- **CSV Handling** - Import/export utilities
- **Email** - Template-based emails, Amazon SES integration
- **REST Client** - HTTP client for API integrations
- **Security** - Encryption/decryption, IP validation
- **Validation** - Custom rules (hostname, IP, CIDR, datetime, paths)
- **Session Management** - Database-backed sessions with custom columns
- **Logging** - Enhanced logging with context
- **Template Engine** - Twig integration with session variables

### AWS Integration
- **Amazon Rekognition** - Face detection, comparison, and analysis
- **Amazon SES** - Reliable email delivery service

## Requirements

- **PHP** 7.3.0 or later
- **Composer**
- **PHP Extensions:**
  - php-gd
  - php-mbstring
  - php-xml
  - php-imagick (optional, for GIF operations)

### Optional: ImageMagick Installation

Required for `extractFirstFrameOfGif` method in `\X\Util\ImageHelper`.

**Amazon Linux 2:**
```sh
sudo yum -y install ImageMagick php-imagick
```

**Amazon Linux 2023:**
```sh
# Install ImageMagick and PECL
sudo dnf -y install ImageMagick ImageMagick-devel php-pear.noarch

# Install imagick extension
sudo pecl install imagick
echo "extension=imagick.so" | sudo tee -a /etc/php.ini

# Restart services
sudo systemctl restart nginx php-fpm
```

## Installation

Create a new project using Composer:

```sh
composer create-project takuya-motoshima/codeigniter-extension myapp
cd myapp
```

## Quick Start

### 1. Set Permissions

```sh
sudo chmod -R 755 public/upload application/{logs,cache,session}
sudo chown -R nginx:nginx public/upload application/{logs,cache,session}
```

### 2. Configure Web Server

Copy the Nginx configuration:

```sh
sudo cp nginx.sample.conf /etc/nginx/conf.d/myapp.conf
sudo systemctl restart nginx
```

### 3. Set Up Database

Import the database schema:

```sh
mysql -u root -p your_database < skeleton/init.sql
```

### 4. Build Frontend Assets

```sh
cd client
npm install
npm run build
```

### 5. Access Application

Open `http://{your-server-ip}:3000/` in your browser.

**Default Credentials:**
- Email: `robin@example.com`
- Password: `password`

### Screenshots

<p align="left">
  <img alt="Sign In" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/sign-in.png" width="45%">
  <img alt="User List" src="https://raw.githubusercontent.com/takuya-motoshima/codeigniter-extension/master/screencaps/list-of-users.png" width="45%">
</p>

## Configuration

### Basic Config (`application/config/config.php`)

<table>
  <thead>
    <tr>
      <th>Setting</th>
      <th>Default</th>
      <th>Recommended</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>base_url</td>
      <td><em>empty</em></td>
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
      <td><em>empty</em></td>
    </tr>
  </tbody>
</table>

### Access Control Setup

#### 1. Define Default Route

In `application/config/routes.php`:

```php
$route['default_controller'] = 'users/login';
```

#### 2. Set Session Constant

In `application/config/constants.php`:

```php
const SESSION_NAME = 'session';
```

#### 3. Configure Hooks

In `application/config/hooks.php`:

```php
use \X\Annotation\AnnotationReader;
use \X\Util\Logger;

$hook['post_controller_constructor'] = function() {
  if (is_cli()) return;

  $CI =& get_instance();
  $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
  $loggedin = !empty($_SESSION[SESSION_NAME]);

  if (!$meta->allow_http)
    throw new \RuntimeException('HTTP access is not allowed');
  else if ($loggedin && !$meta->allow_login)
    redirect('/users/index');
  else if (!$loggedin && !$meta->allow_logoff)
    redirect('/users/login');
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

## Usage Examples

### Controllers

```php
use \X\Annotation\Access;

class Users extends AppController {
  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function index() {
    $users = $this->UserModel->get()->result_array();
    parent::set('users', $users)->view('users/index');
  }

  /**
   * @Access(allow_http=true)
   */
  public function api() {
    $data = ['message' => 'Success'];
    parent::set($data)->json();
  }
}
```

### Models

```php
class UserModel extends AppModel {
  const TABLE = 'user';

  public function getActiveUsers() {
    return $this
      ->where('active', 1)
      ->order_by('name', 'ASC')
      ->get()
      ->result_array();
  }
}
```

### Twig Templates

Session variables are automatically available:

```php
// PHP
$_SESSION['user'] = ['name' => 'John Smith', 'role' => 'admin'];
```

```twig
{# Template #}
{% if session.user is defined %}
  <p>Welcome, {{ session.user.name }}!</p>
  {% if session.user.role == 'admin' %}
    <a href="/admin">Admin Panel</a>
  {% endif %}
{% endif %}
```

### Using Utilities

```php
// Image processing
use \X\Util\ImageHelper;
ImageHelper::resize('/path/to/image.jpg', '/path/to/output.jpg', 800, 600);

// File operations
use \X\Util\FileHelper;
FileHelper::makeDirectory('/path/to/dir', 0755);

// Encryption
use \X\Util\Cipher;
$encrypted = Cipher::encrypt('secret data', 'encryption-key');

// REST client
use \X\Util\RestClient;
$client = new RestClient(['base_url' => 'https://api.example.com']);
$response = $client->get('/users');
```

## Testing

Run unit tests:

```sh
composer test
```

Test files are located in:
- `__tests__/*.php` - Test cases
- `phpunit.xml` - Configuration
- `phpunit-printer.yml` - Output format

## Documentation

- **[API Documentation](https://takuya-motoshima.github.io/codeigniter-extension/)** - Complete API reference
- **[Demo Application](demo/)** - Full working example
- **[Changelog](CHANGELOG.md)** - Version history and changes
- **[CodeIgniter 3 Guide](https://codeigniter.com/userguide3/)** - Official framework documentation

### Generate PHPDoc

```sh
# Download phpDocumentor (one-time)
wget https://phpdoc.org/phpDocumentor.phar
chmod +x phpDocumentor.phar

# Generate docs
php phpDocumentor.phar run -d src/ --ignore vendor --ignore src/X/Database/Driver/ -t docs/
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Author

**Takuya Motoshima**
- GitHub: [@takuya-motoshima](https://github.com/takuya-motoshima)
- Twitter: [@TakuyaMotoshima](https://x.com/takuya_motech)
- Facebook: [takuya.motoshima.7](https://www.facebook.com/takuya.motoshima.7)

## License

[MIT License](LICENSE)
