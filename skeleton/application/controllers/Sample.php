<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample extends AppController {

  protected $model = 'SampleModel';

  public function index() {
    parent::view('sample');
  }
}