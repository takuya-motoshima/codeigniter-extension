<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\FileHelper;
use \X\Util\Logger;

class FileHelperTest extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function index() {
    try {
      chdir(APPPATH . 'test_data');
      $group = 'nginx';
      $user = 'nginx';

      // Move files.
      touch('file1.txt');
      FileHelper::move('file1.txt', 'newfile1.txt');

      // Specify the group and owner of the moved file.
      touch('file2.txt');
      FileHelper::move('file2.txt', 'newfile2.txt', $group, $owner);

      // Specify the group of files after moving.
      touch('file3.txt');
      FileHelper::move('file3.txt', 'newfile3.txt', $group);

      // Specify the owner of the moved file
      touch('file4.txt');
      FileHelper::move('file4.txt', 'newfile4.txt', null, $owner);

      // Copy files.
      touch('file5.txt');
      FileHelper::copyFile('file5.txt', 'newfile5.txt');

      // Specify the group and owner of the copied file.
      touch('file6.txt');
      FileHelper::copyFile('file6.txt', 'newfile6.txt', $group, $user);

      // Specify the group of files after copying.
      touch('file7.txt');
      FileHelper::copyFile('file7.txt', 'newfile7.txt', $group);

      // Specify the owner of the copied file
      touch('file8.txt');
      FileHelper::copyFile('file8.txt', 'newfile8.txt', null, $user);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}