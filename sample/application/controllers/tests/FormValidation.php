<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Rekognition\Client;

class FormValidation extends AppController {
  public function index() {
    try {
      $this->form_validation
        ->set_data([
          'dir1' => '/',
          'dir2' => '/abc',
          'dir3' => '/sab_',
          'dir4' => '/abc/abc/',
          'dir5' => '/sad/dfsd',
          'dir6' => 'null',
          'dir7' => '/dsf/dfsdf/dsfsf/sdfds',
          'dir8' => '/e3r/343/8437',
          'dir9' => '/4333/32#',
          'ip1' => '192.168.0.1',      // valid
          'ip2' => '192.168.0.1/',     // invalid
          'ip3' => '192.168.0.1/0',    // valid
          'ip4' => '192.168.0.1/1',    // valid  
          'ip5' => '192.168.0.1/2',    // valid  
          'ip6' => '192.168.0.1/3',    // valid  
          'ip7' => '192.168.0.1/4',    // valid  
          'ip8' => '192.168.0.1/5',    // valid  
          'ip9' => '192.168.0.1/6',    // valid  
          'ip10' => '192.168.0.1/7',   // valid  
          'ip11' => '192.168.0.1/8',   // valid  
          'ip12' => '192.168.0.1/9',   // valid  
          'ip13' => '192.168.0.1/10',  // valid  
          'ip14' => '192.168.0.1/11',  // valid  
          'ip15' => '192.168.0.1/12',  // valid  
          'ip16' => '192.168.0.1/13',  // valid  
          'ip17' => '192.168.0.1/14',  // valid  
          'ip18' => '192.168.0.1/15',  // valid  
          'ip19' => '192.168.0.1/16',  // valid  
          'ip20' => '192.168.0.1/17',  // valid  
          'ip21' => '192.168.0.1/18',  // valid  
          'ip22' => '192.168.0.1/19',  // valid  
          'ip23' => '192.168.0.1/20',  // valid  
          'ip24' => '192.168.0.1/21',  // valid  
          'ip25' => '192.168.0.1/22',  // valid  
          'ip26' => '192.168.0.1/23',  // valid  
          'ip27' => '192.168.0.1/24',  // valid  
          'ip28' => '192.168.0.1/25',  // valid  
          'ip29' => '192.168.0.1/26',  // valid  
          'ip30' => '192.168.0.1/27',  // valid  
          'ip31' => '192.168.0.1/28',  // valid  
          'ip32' => '192.168.0.1/29',  // valid  
          'ip33' => '192.168.0.1/30',  // valid  
          'ip34' => '192.168.0.1/31',  // valid  
          'ip35' => '192.168.0.1/32',  // valid  
          'ip36' => '192.168.0.1/33',  // invalid
          'ip37' => '192.168.0.1/34',  // invalid
          'ip38' => '192.168.0.1/asd', // invalid
          'ip39' => '192.168.0.1/01',  // invalid
          'ip40' => '192.168.0.1/00',  // invalid;
        ])
        ->set_rules('dir1', 'dir1', 'directory_path')
        ->set_rules('dir2', 'dir2', 'directory_path')
        ->set_rules('dir3', 'dir3', 'directory_path')
        ->set_rules('dir4', 'dir4', 'directory_path')
        ->set_rules('dir5', 'dir5', 'directory_path')
        ->set_rules('dir6', 'dir6', 'directory_path')
        ->set_rules('dir7', 'dir7', 'directory_path')
        ->set_rules('dir8', 'dir8', 'directory_path')
        ->set_rules('dir9', 'dir9', 'directory_path')
        ->set_rules('ip1', 'ip1', 'ipaddress_or_cidr')
        ->set_rules('ip2', 'ip2', 'ipaddress_or_cidr')
        ->set_rules('ip3', 'ip3', 'ipaddress_or_cidr')
        ->set_rules('ip4', 'ip4', 'ipaddress_or_cidr')
        ->set_rules('ip5', 'ip5', 'ipaddress_or_cidr')
        ->set_rules('ip6', 'ip6', 'ipaddress_or_cidr')
        ->set_rules('ip7', 'ip7', 'ipaddress_or_cidr')
        ->set_rules('ip8', 'ip8', 'ipaddress_or_cidr')
        ->set_rules('ip9', 'ip9', 'ipaddress_or_cidr')
        ->set_rules('ip10', 'ip10', 'ipaddress_or_cidr')
        ->set_rules('ip11', 'ip11', 'ipaddress_or_cidr')
        ->set_rules('ip12', 'ip12', 'ipaddress_or_cidr')
        ->set_rules('ip13', 'ip13', 'ipaddress_or_cidr')
        ->set_rules('ip14', 'ip14', 'ipaddress_or_cidr')
        ->set_rules('ip15', 'ip15', 'ipaddress_or_cidr')
        ->set_rules('ip16', 'ip16', 'ipaddress_or_cidr')
        ->set_rules('ip17', 'ip17', 'ipaddress_or_cidr')
        ->set_rules('ip18', 'ip18', 'ipaddress_or_cidr')
        ->set_rules('ip19', 'ip19', 'ipaddress_or_cidr')
        ->set_rules('ip20', 'ip20', 'ipaddress_or_cidr')
        ->set_rules('ip21', 'ip21', 'ipaddress_or_cidr')
        ->set_rules('ip22', 'ip22', 'ipaddress_or_cidr')
        ->set_rules('ip23', 'ip23', 'ipaddress_or_cidr')
        ->set_rules('ip24', 'ip24', 'ipaddress_or_cidr')
        ->set_rules('ip25', 'ip25', 'ipaddress_or_cidr')
        ->set_rules('ip26', 'ip26', 'ipaddress_or_cidr')
        ->set_rules('ip27', 'ip27', 'ipaddress_or_cidr')
        ->set_rules('ip28', 'ip28', 'ipaddress_or_cidr')
        ->set_rules('ip29', 'ip29', 'ipaddress_or_cidr')
        ->set_rules('ip30', 'ip30', 'ipaddress_or_cidr')
        ->set_rules('ip31', 'ip31', 'ipaddress_or_cidr')
        ->set_rules('ip32', 'ip32', 'ipaddress_or_cidr')
        ->set_rules('ip33', 'ip33', 'ipaddress_or_cidr')
        ->set_rules('ip34', 'ip34', 'ipaddress_or_cidr')
        ->set_rules('ip35', 'ip35', 'ipaddress_or_cidr')
        ->set_rules('ip36', 'ip36', 'ipaddress_or_cidr')
        ->set_rules('ip37', 'ip37', 'ipaddress_or_cidr')
        ->set_rules('ip38', 'ip38', 'ipaddress_or_cidr')
        ->set_rules('ip39', 'ip39', 'ipaddress_or_cidr')
        ->set_rules('ip40', 'ip40', 'ipaddress_or_cidr');

      // Output: Error message: Array
      // (
      //     [dir4] => The dir4 field must contain a valid directory path.
      //     [dir6] => The dir6 field must contain a valid directory path.
      //     [dir9] => The dir9 field must contain a valid directory path.
      //     [ip2] => The ip2 field must contain a valid ip address or CIDR.
      //     [ip36] => The ip36 field must contain a valid ip address or CIDR.
      //     [ip37] => The ip37 field must contain a valid ip address or CIDR.
      //     [ip38] => The ip38 field must contain a valid ip address or CIDR.
      //     [ip39] => The ip39 field must contain a valid ip address or CIDR.
      //     [ip40] => The ip40 field must contain a valid ip address or CIDR.
      // )
      if ($this->form_validation->run() != false)
        Logger::print('There are no errors.');
      else
        Logger::print('Error message: ', $this->form_validation->error_array());
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}