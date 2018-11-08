<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

/**
 * TestModel
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
class TestModel extends \AppModel
{

  protected $table = 'user';

  /**
   * Get data 
   * @return [type] [description]
   */
  public function getItems()
  {
    return parent
      ::from($this->table)
      ::get()
      ->result_array();
  }

  /**
   * 
   * Transaction test
   *
   * @param string $email
   * @return int
   */
  public function transaction()
  {
    try {

      parent::trans_begin();
      parent::insert($this->table, ['name' => 'foo']);
      parent::insert($this->table, ['name' => 'bar']);
      if (!parent::trans_status()) {
        throw \RuntimeException(parent::error()['message']);
      }
      parent::trans_commit();
    } catch (Throwable $e) {
      parent::trans_rollback();
      Logger::s('Record after rollback=', parent::get($this->table)->result_array());
      throw $e;
    }
  }
}