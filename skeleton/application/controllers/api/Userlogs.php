<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class Userlogs extends AppController {
  protected $model = 'UserLogModel';

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
   */
  public function query() {
    try {
      $this->form_validation
        ->set_data($this->input->get())
        ->set_rules('start', 'start', 'required|is_natural')
        ->set_rules('length', 'length', 'required|is_natural')
        ->set_rules('order', 'order', 'required')
        ->set_rules('dir', 'dir', 'required|in_list[asc,desc]');
      if (!$this->form_validation->run())
        throw new \RuntimeException('Invalid parameter');
      $data = $this->UserLogModel->paginate(
        $this->input->get('start'),
        $this->input->get('length'),
        $this->input->get('order'),
        $this->input->get('dir'),
        $this->input->get('search')
      );
      $data['draw'] = $this->input->get('draw');
      parent::set($data)::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }
}