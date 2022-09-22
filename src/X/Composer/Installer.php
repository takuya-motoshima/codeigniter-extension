<?php
namespace X\Composer;

use Composer\Script\Event;
use Composer\IO\ConsoleIO;
use X\Util\FileHelper;

final class Installer {
  const DOCUMENT_ROOT = 'public/';
  const FRAMEWORK_DIR = 'vendor/codeigniter/framework/';

  /**
   * Composer post install script
   */
  public static function post_install(Event $event) {
    $io = $event->getIO();
    FileHelper::copyDirectory(static::FRAMEWORK_DIR . 'application', 'application');
    FileHelper::copyDirectory('skeleton/application', 'application');
    FileHelper::copyFile(static::FRAMEWORK_DIR . 'index.php', static::DOCUMENT_ROOT . 'index.php');
    FileHelper::copyDirectory('skeleton/public', static::DOCUMENT_ROOT);
    // FileHelper::copyFile('skeleton/public/.htaccess', static::DOCUMENT_ROOT . '.htaccess');
    // FileHelper::copyFile('skeleton/.gitignore', '.gitignore');
    // FileHelper::copyFile('skeleton/.gitattributes', '.gitattributes');
    self::update_index($io);
    self::update_config($io);
    self::composer_update($io);
    self::show_message($io);
    self::delete_self();
  }

  /**
   * Update index.php
   */
  private static function update_index(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>Update public/index.php is running');
    FileHelper::replace(static::DOCUMENT_ROOT . 'index.php', [
      '$system_path = \'system\';' => '$system_path = \'../' . static::FRAMEWORK_DIR . 'system\';',
      '$application_folder = \'application\';' => '$application_folder = \'../application\';',
    ]);
    $io->write('<info>Update public/index.php succeeded');
    $io->write('==================================================');
  }

  /**
   * Update application/config/config.php
   */
  private static function update_config(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>Update application/config/config.php is running');
    FileHelper::replace('application/config/config.php', [
      '$config[\'base_url\'] = \'\';' => 'if (!empty($_SERVER[\'HTTP_HOST\'])) {$config[\'base_url\'] = "//".$_SERVER[\'HTTP_HOST\'] . str_replace(basename($_SERVER[\'SCRIPT_NAME\']),"",$_SERVER[\'SCRIPT_NAME\']);}',
      '$config[\'enable_hooks\'] = FALSE;' => '$config[\'enable_hooks\'] = TRUE;',
      '$config[\'permitted_uri_chars\'] = \'a-z 0-9~%.:_\-\';' => '$config[\'permitted_uri_chars\'] = \'a-z 0-9~%.:_\-,\';',
      '$config[\'sess_save_path\'] = NULL;' => '$config[\'sess_save_path\'] = \APPPATH . \'session\';',
      '$config[\'cookie_httponly\']  = FALSE;' => '$config[\'cookie_httponly\']  = TRUE;',
      '$config[\'composer_autoload\'] = FALSE;' => '$config[\'composer_autoload\'] = realpath(\APPPATH . \'../vendor/autoload.php\');',
      '$config[\'index_page\'] = \'index.php\';' => '$config[\'index_page\'] = \'\';',
      '$config[\'subclass_prefix\'] = \'MY_\';' => '$config[\'subclass_prefix\'] = \'App\';',
    ]);
    FileHelper::replace('application/config/autoload.php', [
      '$autoload[\'helper\'] = array();' => '$autoload[\'helper\'] = array(\'url\');',
    ]);
    $io->write('<info>Update application/config/config.php succeeded');
    $io->write('==================================================');
  }

  /**
   * Composer update
   */
  private static function composer_update(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>Composer update is running');
    FileHelper::copyFile('composer.json.dist', 'composer.json');
    FileHelper::copyFile('.env.dist', '.env');
    passthru('composer update');
    $io->write('<info>Composer update is succeeded');
    $io->write('==================================================');
  }

  /**
   * Show message
   */
  private static function show_message(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>`public/.htaccess` was installed. If you don\'t need it, please remove it.</info>');
    $io->write('See <https://packagist.org/packages/takuya-motoshima/codeigniter-extensions> for details');
    $io->write('==================================================');
  }

  /**
   * Delete self
   */
  private static function delete_self() {
    FileHelper::delete(
      'src',
      'sample',
      'composer.json.dist',
      'skeleton',
      'README.md',
      'LICENSE.md'
    );
  }
}