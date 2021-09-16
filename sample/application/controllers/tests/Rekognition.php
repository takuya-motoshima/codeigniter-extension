<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Rekognition\Client;

class Rekognition extends AppController {
  public function index() {
    try {
      // Rekognition client instance.
      $client = new Client([
        'region' => $_ENV['AMAZON_REKOGNITION_REGION'],
        'key' => $_ENV['AMAZON_REKOGNITION_ACCESS_KEY'],
        'secret' => $_ENV['AMAZON_REKOGNITION_SECRET_KEY'],
        'debug' => true
      ]);

      // The path of the image to analyze.
      $imgPath = APPPATH . 'test_data/drive.jpg';

      // Detect face from image.
      $result = $client->detectionFaces($imgPath);
      Logger::print($result);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}