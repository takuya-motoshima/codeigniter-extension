<?php
use PHPUnit\Framework\TestCase;
final class Sample extends TestCase {

  private $object;

  // Before a test method is run, a template method called setUp() is invoked.
  // setUp() is where you create the objects against which you will test. 
  protected function setUp(): void {
    $this->object = new X\SampleTest('hello, world');
  }

  // Once the test method has finished running, whether it succeeded or failed, another template method called tearDown() is invoked. 
  // tearDown() is where you clean up the objects against which you tested.
  protected function tearDown(): void {
    // do nothing
    unset($this->object);
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
    $this->assertEquals('hello, world', $this->object->getMessage());
  }

  public function testSetMessage(): void {
    $this->object->setMessage('good-bye, world');
    $this->assertEquals('good-bye, world', $this->object->getMessage());
  }
}
