<?php
use PHPUnit\Framework\TestCase;

final class SampleTest extends TestCase {
  private $sample;

  protected function setUp(): void {
    $this->sample = new \X\UnittestSample('Hello world');
  }

  protected function tearDown(): void {
    unset($this->sample);
  }

  public static function setUpBeforeClass(): void {
  }

  public static function tearDownAfterClass(): void {
  }

  public function testGetMessage(): void {
    $this->assertEquals('Hello world', $this->sample->getMessage());
  }

  public function testSetMessage(): void {
    $this->sample->setMessage('Bye world');
    $this->assertEquals('Bye world', $this->sample->getMessage());
  }
}