<?php
use PHPUnit\Framework\TestCase;
final class UnittestSample extends TestCase {
  private $unittestSample;

  protected function setUp(): void {
    $this->unittestSample = new X\UnittestSample('hello, world');
  }

  protected function tearDown(): void {
    // do nothing
    unset($this->unittestSample);
  }

  public static function setUpBeforeClass(): void {
    // do nothing
  }

  public static function tearDownAfterClass(): void {
    // do nothing
  }

  public function testGetMessage(): void {
    $this->assertEquals('hello, world', $this->unittestSample->getMessage());
  }

  public function testSetMessage(): void {
    $this->unittestSample->setMessage('good-bye, world');
    $this->assertEquals('good-bye, world', $this->unittestSample->getMessage());
  }
}