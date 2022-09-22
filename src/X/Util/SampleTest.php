<?php
namespace X;

class SampleTest {
  private $message;

  public function __construct(string $message) {
    $this->message = $message;
  }

  public function getMessage(): string {
    return $this->message;
  }

  public function setMessage(string $message): void {
    $this->message = $message;
  }
}