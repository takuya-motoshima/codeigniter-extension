<?php
use PHPUnit\Framework\TestCase;
final class UnittestSample extends TestCase {

  private $unittestSample;

  // Before a test method is run, a template method called setUp() is invoked.
  // setUp() is where you create the objects against which you will test. 
  protected function setUp(): void {
    $this->unittestSample = new X\UnittestSample('hello, world');
  }

  // Once the test method has finished running, whether it succeeded or failed, another template method called tearDown() is invoked. 
  // tearDown() is where you clean up the objects against which you tested.
  protected function tearDown(): void {
    // do nothing
    unset($this->unittestSample);
  }


  // setUpBeforeClass()  template method is called before the first test of the test case class is run, respectively.
  public static function setUpBeforeClass(): void {
    // do nothing
  }

  // tearDownAfterClass() template method is called after the last test of the test case class is run, respectively.
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