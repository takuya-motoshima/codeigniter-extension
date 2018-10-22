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

    protected $table = 'test';

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
            Logger::s('Record before rollback=', parent::get($this->table)->result_array());
            throw new RuntimeException('Deliberate error');
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