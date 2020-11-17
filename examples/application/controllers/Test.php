<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\FileHelper;
use \X\Util\IpUtils;

class Test extends AppController {

  protected $model = 'TestModel';

  public function index() {
    parent::view('test');
  }

  public function log() {
    Logger::debug('Test message');
    Logger::info('Test message');
    Logger::error('Test message');
    Logger::print('Test message');
    Logger::printWithoutPath('Test message');
  }

  public function file() {
    // Calculate file size.
    $size = FileHelper::getDirectorySize([
      APPPATH . 'sample_data/animals',
      APPPATH . 'sample_data/transport'
    ]);
    Logger::print('The size of sample_data/animals and sample_data/transport is ' . $size);

    // Calculate file size.
    $size = FileHelper::getDirectorySize(APPPATH . 'sample_data/animals');
    Logger::print('The size of sample_data/animals is ' . $size);

    // File copy.
    FileHelper::copyDirectory(APPPATH . 'sample_data/animals', APPPATH . 'sample_data/result');
  }

  public function ziplibrary() {
    Logger::debug('Memory at start: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at start: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
    $targetDir = APPPATH . 'sample_data';
    $files = FileHelper::find($targetDir . '/*');
    $this->load->library('zip');
    foreach ($files as $file) $this->zip->add_data($file, file_get_contents($targetDir . '/' . $file));
    $archivePath = tempnam(sys_get_temp_dir(), uniqid());
    $this->zip->archive($archivePath);
    Logger::debug('Memory at end: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at end: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
    $this->zip->download('sample_data.zip');
  }

  public function zipstream() {
    Logger::debug('Memory at start: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at start: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
    $options = new ZipStream\Option\Archive();
    $options->setSendHttpHeaders(true);
    $fileopt = new ZipStream\Option\File();
    $fileopt->setMethod(ZipStream\Option\Method::STORE());
    $zip = new ZipStream\ZipStream('sample_data.zip', $options);
    $targetDir = APPPATH . 'sample_data';
    $files = FileHelper::find($targetDir . '/*');
    foreach($files as $file) $zip->addFileFromPath($file, $targetDir . '/' . $file, $fileopt);
    $zip->finish();
    Logger::debug('Memory at end: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at end: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
  }

  public function subquery() {
    $results = $this->TestModel->getUsingSubquery();
    Logger::print($results);
  }

  public function transaction() {
    try {
      // Transaction start
      $this->TestModel->trans_begin();

      // Added "Human" and "insect"
      $this->TestModel->saveRows([[ 'thing' => 'Human' ], [ 'thing' => 'insect' ],]);
      Logger::print('Added "Human" and "insect": ', $this->TestModel->getAll());

      // Update "Human" to "Squid"
      $this->TestModel->saveRow([ 'id' => 1, 'thing' => 'Squid' ]);
      Logger::print('Update "Human" to "Squid": ', $this->TestModel->getAll());

      // Added "Lion"
      $this->TestModel->addRow([ 'thing' => 'Lion' ]);
      Logger::print('Added "Lion": ', $this->TestModel->getAll());

      // Delete "insect"
      $this->TestModel->deleteRow('insect');
      Logger::print('Delete "insect": ', $this->TestModel->getAll());

      // Update "Squid" to "Whale"
      $this->TestModel->updateRow('Squid', 'Whale');
      Logger::print('Update "Squid" to "Whale": ', $this->TestModel->getAll());

      // Undo
      $this->TestModel->trans_rollback();

      // $this->TestModel->trans_commit();
      // Search test table after rollback
      Logger::print('Test table rolled back: ', $this->TestModel->getAll());
    } catch (\Throwable $e) {
      $this->TestModel->trans_rollback();
      Logger::print($e);
    }
  }

  public function cipher() {
    // Get the initialization vector. This should be changed every time to make it difficult to predict.
    $iv = Cipher::generateInitialVector();

    // Plaintext.
    $plaintext = 'Hello, World.';

    // Encrypted password.
    $password = 'password';

    // Encrypt.
    $encrypted = Cipher::encrypt($plaintext, $password, $iv);
    Logger::print('Encrypted: ', $encrypted);

    // Decrypt.
    $decrypted = Cipher::decrypt($encrypted, $password, $iv);
    Logger::print('Decrypt: ', $decrypted);

    // Compare image sizes before and after encryption.
    $base64 = base64_encode(file_get_contents(APPPATH . 'sample_data/0qmIJOcCtbs.jpg'));
    $originalSize = mb_strlen($base64 , '8bit');
    $decrypted = Cipher::encrypt($base64, $password, $iv);
    $decryptedSize = mb_strlen($decrypted , '8bit');
    Logger::print('Size before encryption: ', $originalSize);
    Logger::print('Size after encryption: ', $decryptedSize);
  }

  public function ip() {
    // Get client ip.
    $ip = IpUtils::getClientIpFromXFF();
    Logger::print('Client IP: ', $ip);
    // IP format test.
    $ips = [
      '234.192.0.2',
      '234.198.51.100',
      '234.203.0.113',
      '0000:0000:0000:0000:0000:ffff:7f00:0001',
      '::1'
    ];
    foreach ($ips as $ip) {
      if (IpUtils::isIPv4($ip)) Logger::print($ip . ' is IPv4');
      else Logger::print($ip . ' is not IPv4');
    }
    foreach ($ips as $ip) {
      if (IpUtils::isIPv6($ip)) Logger::print($ip . ' is IPv6');
      else Logger::print($ip . ' is not IPv6');
    }
    // IP range check.
    $ips = [
      // 202.210.220.64/28
      ['202.210.220.63', '202.210.220.64/28'],// false
      ['202.210.220.64', '202.210.220.64/28'],// true
      ['202.210.220.65', '202.210.220.64/28'],// true
      ['202.210.220.78', '202.210.220.64/28'],// true
      ['202.210.220.79', '202.210.220.64/28'],// true
      ['202.210.220.80', '202.210.220.64/28'],// false
      // 192.168.1.0/24
      ['192.168.0.255', '192.168.1.0/24'], // false
      ['192.168.1.0', '192.168.1.0/24'], // true
      ['192.168.1.1', '192.168.1.0/24'], // true
      ['192.168.1.244', '192.168.1.0/24'], // true
      ['192.168.1.255', '192.168.1.0/24'], // true
      ['192.168.2.0', '192.168.1.0/24'], // false
      // 118.238.251.130
      ['118.238.251.129', '118.238.251.130'], // false
      ['118.238.251.130', '118.238.251.130'], // true
      ['118.238.251.131', '118.238.251.130'], // false
      // 118.238.251.130/32
      ['118.238.251.129', '118.238.251.130/32'], // false
      ['118.238.251.130', '118.238.251.130/32'], // true
      ['118.238.251.131', '118.238.251.130/32'], // false
      // 2001:4860:4860::8888/32
      ['2001:4859:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32'],// false
      ['2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860::8888/32'],// true
      ['2001:4860:4860:0000:0000:0000:0000:8889', '2001:4860:4860::8888/32'],// true
      ['2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFE', '2001:4860:4860::8888/32'],// true
      ['2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32'],// true
      ['2001:4861:0000:0000:0000:0000:0000:0000', '2001:4860:4860::8888/32'],// false
      // 2404:7a81:b0a0:9100::/64
      ['2404:7A81:B0A0:90FF:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64'],// false
      ['2404:7A81:B0A0:9100:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64'],// true
      ['2404:7A81:B0A0:9100:0000:0000:0000:0001', '2404:7A81:B0A0:9100::/64'],// true
      ['2404:7A81:B0A0:9100:A888:5EE2:EA92:B618', '2404:7A81:B0A0:9100::/64'],// true
      ['2404:7A81:B0A0:9100:D03:959E:7F47:9B77', '2404:7A81:B0A0:9100::/64'],// true
      ['2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFE', '2404:7A81:B0A0:9100::/64'],// true
      ['2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFF', '2404:7A81:B0A0:9100::/64'],// true
      ['2404:7A81:B0A0:9101:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64'],// false
    ];
    foreach ($ips as $ip) {
      $requestIp = $ip[0];
      $range = $ip[1];
      $matched = IpUtils::inRange($requestIp, $range);
      Logger::print(sprintf('%s in %s: %s', $requestIp, $range, $matched ? 'true' : 'false'));
    }
  }
}