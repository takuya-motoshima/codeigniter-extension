<?php
use PHPUnit\Framework\TestCase;
final class UnittestSample extends TestCase {
  private $unittestSample;

  protected function setUp(): void {
    $this->unittestSample = new X\UnittestSample('Hello world');
  }

  protected function tearDown(): void {
    unset($this->unittestSample);
  }

  public static function setUpBeforeClass(): void {
  }

  public static function tearDownAfterClass(): void {
  }

  public function testGetMessage(): void {
    $this->assertEquals('Hello world', $this->unittestSample->getMessage());
  }

  public function testSetMessage(): void {
    $this->unittestSample->setMessage('Bye world');
    $this->assertEquals('Bye world', $this->unittestSample->getMessage());
  }
}