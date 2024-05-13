<?php
use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\ImageHelper;

class Test extends AppController {
  protected $model = 'UserModel';

  /**
   * @Access(allow_http=false)
   * @example php public/index.php test/form_validation
   */
  public function form_validation() {
    // // Check the host name.
    // $valid = $this->form_validation
    //   ->set_data([
    //     'hostname1' => 'example.com',
    //     'hostname2' => 'localhost',
    //     'hostname3' => 'c-61-123-45-67.hsd1.co.comcast.net',
    //     'hostname4' => 'example',
    //   ])
    //   ->set_rules('hostname1', 'hostname1', 'required|hostname')
    //   ->set_rules('hostname2', 'hostname2', 'required|hostname')
    //   ->set_rules('hostname3', 'hostname3', 'required|hostname')
    //   ->set_rules('hostname4', 'hostname4', 'required|hostname')
    //   ->run();
    // Logger::display('Host name check result=', var_export($valid, true));
    // Logger::display('Host name input error:', $this->form_validation->error_array());

    // // Reset form validation.
    // $this->form_validation->reset_validation();

    // Check the file path.
    $valid = $this->form_validation
      ->set_data([
        'path1' => '/usr/lib',// It should be valid.
        'path2' => 'usr/lib',// It should be valid.
        'path3' => '/usr/lib',// It should be invalid.
        'path4' => 'usr/lib',// It should be valid.
      ])
      ->set_rules('path1', 'path1', 'is_path')
      ->set_rules('path2', 'path2', 'is_path')
      ->set_rules('path3', 'path3', 'is_path[true]')
      ->set_rules('path4', 'path4', 'is_path[true]')
      ->run();
    Logger::display('Path check result=', var_export($valid, true));
    if (!$valid)
      Logger::display('Path input error:', $this->form_validation->error_array());
  }

  /**
   * @Access(allow_http=false)
   * @example CI_ENV=development php public/index.php test/sanitize_sql
   */
  public function sanitize_sql() {
    $this->UserModel
      ->where('id', 1)
      ->get()
      ->row_array();
    $sql = $this->UserModel->last_query();
    Logger::display($sql);// => SELECT * FROM `user` WHERE `id` = 1
  
    $this->UserModel
      ->where('id', '\'1\' OR id=2')
      ->get()
      ->row_array();
    $sql = $this->UserModel->last_query();
    Logger::display($sql);
    // => SELECT * FROM `user` WHERE `id` = '\'1\' OR id=2'

    $this->UserModel
      ->where('id', '\'1\' OR id=2', FALSE)
      ->get()
      ->row_array();
    $sql = $this->UserModel->last_query();
    Logger::display($sql);
    // => SELECT * FROM `user` WHERE id =  '1' OR id=2
  }

  /**
   * @Access(allow_http=true)
   */
  public function api() {
    parent
      ::set('message', 'This is a test.')
      ::json();
  }
}