<?php
/**
 * Part of CodeIgniter Composer Installer
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Composer;
use Composer\Script\Event;
use Composer\IO\ConsoleIO;
use X\Util\FileHelper;
final class Installer {
  /**
   * @var string DOCUMENT_ROOT
   */
  const DOCUMENT_ROOT = 'public/';

  /**
   * @var string FRAMEWORK_DIR
   */
  const FRAMEWORK_DIR = 'vendor/codeigniter/framework/';

  /**
   * Composer post install script
   *
   * @param Event $event
   */
  public static function post_install(Event $event) {
    $io = $event->getIO();
    FileHelper::copyDirectory(static::FRAMEWORK_DIR . 'application', 'application');
    FileHelper::copyDirectory('skeleton/application', 'application');
    FileHelper::copyFile(static::FRAMEWORK_DIR . 'index.php', static::DOCUMENT_ROOT . 'index.php');
    FileHelper::copyFile('skeleton/public/.htaccess', static::DOCUMENT_ROOT . '.htaccess');
    FileHelper::copyFile('skeleton/.gitignore', '.gitignore');
    FileHelper::copyFile('skeleton/.gitattributes', '.gitattributes');
    self::update_index($io);
    self::update_config($io);
    self::composer_update($io);
    self::show_message($io);
    self::delete_self();
  }

  /**
   * Update index.php
   *
   * @param ConsoleIO $io
   * @return void
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
   *
   * @param ConsoleIO $io
   * @return void
   */
  private static function update_config(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>Update application/config/config.php is running');
    FileHelper::replace('application/config/config.php', [
      // Base Site URL
      '$config[\'base_url\'] = \'\';' => 'if (!empty($_SERVER[\'HTTP_HOST\'])) {$config[\'base_url\'] = "//".$_SERVER[\'HTTP_HOST\'] . str_replace(basename($_SERVER[\'SCRIPT_NAME\']),"",$_SERVER[\'SCRIPT_NAME\']);}',
      // Enable/Disable System Hooks
      '$config[\'enable_hooks\'] = FALSE;' => '$config[\'enable_hooks\'] = TRUE;',
      // Allowed URL Characters
      '$config[\'permitted_uri_chars\'] = \'a-z 0-9~%.:_\-\';' => '$config[\'permitted_uri_chars\'] = \'a-z 0-9~%.:_\-,\';',
      // Session Variables
      '$config[\'sess_save_path\'] = NULL;' => '$config[\'sess_save_path\'] = \APPPATH . \'session\';',
      // Cookie Related Variables
      '$config[\'cookie_httponly\']  = FALSE;' => '$config[\'cookie_httponly\']  = TRUE;',
      // Composer auto-loading
      '$config[\'composer_autoload\'] = FALSE;' => '$config[\'composer_autoload\'] = realpath(\APPPATH . \'../vendor/autoload.php\');',
      // Index File
      '$config[\'index_page\'] = \'index.php\';' => '$config[\'index_page\'] = \'\';',
      // Class Extension Prefix
      '$config[\'subclass_prefix\'] = \'MY_\';' => '$config[\'subclass_prefix\'] = \'App\';',
    ]);
    FileHelper::replace('application/config/autoload.php', [
      // Auto-load Helper Files
      '$autoload[\'helper\'] = array();' => '$autoload[\'helper\'] = array(\'url\');',
    ]);
    $io->write('<info>Update application/config/config.php succeeded');
    $io->write('==================================================');
  }

  /**
   * Composer update
   *
   * @param ConsoleIO $io
   * @return void
   */
  private static function composer_update(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>Composer update is running');
    FileHelper::copyFile('composer.json.dist', 'composer.json');
    passthru('composer update');
    $io->write('<info>Composer update is succeeded');
    $io->write('==================================================');
  }

  /**
   * Show message
   *
   * @param ConsoleIO $io
   * @return void
   */
  private static function show_message(ConsoleIO $io) {
    $io->write('==================================================');
    $io->write('<info>`public/.htaccess` was installed. If you don\'t need it, please remove it.</info>');
    $io->write('See <https://packagist.org/packages/takuya-motoshima/codeigniter-extensions> for details');
    $io->write('==================================================');
  }

  /**
   * Delete self
   *
   * @return void
   */
  private static function delete_self() {
    FileHelper::delete(
      'src',
      'examples',
      'composer.json.dist',
      'skeleton',
      'README.md',
      'LICENSE.md'
    );
  }
}