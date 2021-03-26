<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\FileHelper;
use \X\Util\ImageHelper;
use \X\Util\IpUtils;
use \X\Util\AmazonSesClient;
use \X\Util\ArrayHelper;
use \X\Util\DateHelper;
use MathieuViossat\Util\ArrayToTextTable;

class Test extends AppController {

  protected $model = 'TestModel';

  public function index() {
    parent::view('test');
  }

  public function getDaysInMonth() {
    $days = DateHelper::getDaysInMonth(2021, 3, 'Y-m-d');
    parent
      ::set($days)
      ::json(false, true);
  }

  public function sendEmail() {
    try {
      // SES client.
      $ses = new AmazonSesClient([
        'credentials' => [
          'key' => $_ENV['SES_ACCESS_KEY'],
          'secret' => $_ENV['SES_SECRET_KEY'],
        ],
        'configuration' => $_ENV['SES_CONFIGURATION'],
        'region' => $_ENV['SES_REGION']
      ]);

      // Send email.
      $result = $ses
        ->from('from@example.com')
        ->to('to@example.com')
        ->subject('Test email')
        ->message('Hello, World!')
        ->send();
      $messageId = $result->get('MessageId');
      Logger::print("Email sent! Message ID: $messageId");
    } catch(\Throwable $e) {
      Logger::print($e->getMessage());
    }

  }

  public function validate() {
    try {
      $this->form_validation
        ->set_data([
          // Datetime custom validation.
          'datetime' => '2021-02-03 17:46:00',// ok

          // Host name custom validation.
          'hostname1' => 'external.asd1230-123.asd_internal.asd.gm-_ail.com',// ok
          'hostname2' => 'domain.com',// ok
          'hostname3' => 'example.domain.com',// ok
          'hostname4' => 'example.domain-hyphen.com',// ok
          'hostname5' => 'www.domain.com',// ok
          'hostname6' => 'example.museum',// ok
          'hostname7' => 'http://example.com',// ng
          'hostname8' => 'subdomain.-example.com',// ng
          'hostname9' => 'example.com/parameter',// ng
          'hostname10' => 'example.com?anything',// ng

          // IP address custom validation.
          'ipaddress1' => '000.0000.00.00',// ng
          'ipaddress2' => '192.168.1.1',// ok
          'ipaddress3' => '912.456.123.123',// ng

          // Host name or ip address custom validation.
          'hostname_or_ipaddress1' => 'external.asd1230-123.asd_internal.asd.gm-_ail.com',// ok
          'hostname_or_ipaddress2' => 'domain.com',// ok
          'hostname_or_ipaddress3' => 'example.domain.com',// ok
          'hostname_or_ipaddress4' => 'example.domain-hyphen.com',// ok
          'hostname_or_ipaddress5' => 'www.domain.com',// ok
          'hostname_or_ipaddress6' => 'example.museum',// ok
          'hostname_or_ipaddress7' => 'http://example.com',// ng
          'hostname_or_ipaddress8' => 'subdomain.-example.com',// ng
          'hostname_or_ipaddress9' => 'example.com/parameter',// ng
          'hostname_or_ipaddress10' => 'example.com?anything',// ng
          'hostname_or_ipaddress11' => '000.0000.00.00',// ng
          'hostname_or_ipaddress12' => '192.168.1.1',// ok
          'hostname_or_ipaddress13' => '912.456.123.123',// ng

          // UNix user name custom validation.
          'unix_username1' => 'abcd',// ok
          'unix_username2' => 'a123',// ok
          'unix_username3' => 'abc-',// ok
          'unix_username4' => 'a-bc',// ok
          'unix_username5' => 'abc$',// ok
          'unix_username7' => 'a-b$',// ok
          'unix_username8' => '1234',// ng
          'unix_username9' => '1abc',// ng
          'unix_username10' => '-abc',// ng
          'unix_username11' => '$abc',// ng
          'unix_username12' => 'a$bc',// ng

          // Port number custom validation.
          'port1' => '-1',// ng
          'port2' => '0',// ok
          'port3' => '1',// ok
          'port4' => '',// ok
          'port5' => '65534',// ok
          'port6' => '65535',// ok
          'port7' => '65536',// ng
        ])
        ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]')
        ->set_rules('hostname1', 'hostname1', 'hostname')
        ->set_rules('hostname2', 'hostname2', 'hostname')
        ->set_rules('hostname3', 'hostname3', 'hostname')
        ->set_rules('hostname4', 'hostname4', 'hostname')
        ->set_rules('hostname5', 'hostname5', 'hostname')
        ->set_rules('hostname6', 'hostname6', 'hostname')
        ->set_rules('hostname7', 'hostname7', 'hostname')
        ->set_rules('hostname8', 'hostname8', 'hostname')
        ->set_rules('hostname9', 'hostname9', 'hostname')
        ->set_rules('hostname10', 'hostname10', 'hostname')
        ->set_rules('ipaddress1', 'ipaddress1', 'ipaddress')
        ->set_rules('ipaddress2', 'ipaddress2', 'ipaddress')
        ->set_rules('ipaddress3', 'ipaddress3', 'ipaddress')
        ->set_rules('hostname_or_ipaddress1', 'hostname_or_ipaddress1', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress2', 'hostname_or_ipaddress2', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress3', 'hostname_or_ipaddress3', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress4', 'hostname_or_ipaddress4', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress5', 'hostname_or_ipaddress5', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress6', 'hostname_or_ipaddress6', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress7', 'hostname_or_ipaddress7', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress8', 'hostname_or_ipaddress8', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress9', 'hostname_or_ipaddress9', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress10', 'hostname_or_ipaddress10', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress11', 'hostname_or_ipaddress11', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress12', 'hostname_or_ipaddress12', 'hostname_or_ipaddress')
        ->set_rules('hostname_or_ipaddress13', 'hostname_or_ipaddress13', 'hostname_or_ipaddress')
        ->set_rules('unix_username1', 'unix_username1', 'unix_username')
        ->set_rules('unix_username2', 'unix_username2', 'unix_username')
        ->set_rules('unix_username3', 'unix_username3', 'unix_username')
        ->set_rules('unix_username4', 'unix_username4', 'unix_username')
        ->set_rules('unix_username5', 'unix_username5', 'unix_username')
        ->set_rules('unix_username6', 'unix_username6', 'unix_username')
        ->set_rules('unix_username7', 'unix_username7', 'unix_username')
        ->set_rules('unix_username8', 'unix_username8', 'unix_username')
        ->set_rules('unix_username9', 'unix_username9', 'unix_username')
        ->set_rules('unix_username10', 'unix_username10', 'unix_username')
        ->set_rules('unix_username11', 'unix_username11', 'unix_username')
        ->set_rules('unix_username12', 'unix_username12', 'unix_username')
        ->set_rules('port1', 'port1', 'port')
        ->set_rules('port2', 'port2', 'port')
        ->set_rules('port3', 'port3', 'port')
        ->set_rules('port4', 'port4', 'port')
        ->set_rules('port5', 'port5', 'port')
        ->set_rules('port6', 'port6', 'port')
        ->set_rules('port7', 'port7', 'port');
      if ($this->form_validation->run() != false) {
        // put your code here
        Logger::print('There are no errors.');
      } else {
        Logger::print($this->form_validation->error_array());
      }
    } catch (\Throwable $e) {
      Logger::print($e);
    }
  }

  public function createThumbnail() {
    try {
      // resize only the width of the image
      ImageHelper::resize(APPPATH . 'sample_data/0qmIJOcCtbs.jpg', APPPATH . 'sample_data/0qmIJOcCtbs_thumb_1.jpg', 100, null, false);

      // resize only the height of the image
      ImageHelper::resize(APPPATH . 'sample_data/0qmIJOcCtbs.jpg', APPPATH . 'sample_data/0qmIJOcCtbs_thumb_2.jpg', null, 100, false);

      // resize the image to a width of 100 and constrain aspect ratio (auto height)
      ImageHelper::resize(APPPATH . 'sample_data/0qmIJOcCtbs.jpg', APPPATH . 'sample_data/0qmIJOcCtbs_thumb_3.jpg', 100, null, true);

      // resize the image to a height of 100 and constrain aspect ratio (auto width)
      ImageHelper::resize(APPPATH . 'sample_data/0qmIJOcCtbs.jpg', APPPATH . 'sample_data/0qmIJOcCtbs_thumb_4.jpg', null, 100, true);

      Logger::print('Thumbnail creation successful.');
    } catch (\Throwable $e) {
      Logger::print($e);
    }
  }

  public function warningOccurred() {
    try {
      error_reporting(E_ALL);
      ini_set('display_errors', 'On');
      Logger::print('Begin');

      // Warning occurs here
      file_get_contents('not_exists.txt');

      Logger::print('End');
    } catch (\Throwable $e) {
      Logger::print('Error occurred');
    }
  }

  public function noticeOccurred() {
    try {
      error_reporting(E_ALL);
      ini_set('display_errors', 'On');
      Logger::print('Begin');

      // Notice occurs here
      echo $undefined;

      Logger::print('End');
    } catch (\Throwable $e) {
      Logger::print('Error occurred');
    }
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
    $plaintext = 'Hello, World!';

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

  public function arrayToTable() {
    try {
      $arr = [
        [
          'firstname' => 'John',
          'lastname' => 'Mathew',
          'email' => 'John.Mathew@xyz.com'
        ],
        [
          'firstname' => 'Jim',
          'lastname' => 'Parker',
          'email' => 'Jim.Parker@xyz.com'
        ]
      ];
      // $renderer = new ArrayToTextTable(ArrayHelper::isVector($arr) ? $arr : [$arr]);
      // echo $renderer->getTable();
      echo '<pre>' . ArrayHelper::toTable($arr) . '</pre>';
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}