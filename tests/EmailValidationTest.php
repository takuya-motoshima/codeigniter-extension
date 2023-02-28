<?php
use PHPUnit\Framework\TestCase;
use \X\Util\Validation;

final class EmailValidationTest extends TestCase {
  /**
   * @dataProvider emailProvider
   */
  public function testEmailValidation(string $email, bool $expected): void {
    $this->assertSame(Validation::email($email), $expected);
  }

  public function emailProvider(): array {
    return [
      ['email@domain.com', true],
      ['firstname.lastname@domain.com', true],
      ['email@subdomain.domain.com', true],
      ['firstname+lastname@domain.com', true],
      ['email@123.123.123.123', true],
      ['email@[123.123.123.123]', false],
      ['“email”@domain.com', true],
      ['1234567890@domain.com', true],
      ['email@domain-one.com', true],
      ['_______@domain.com', true],
      ['email@domain.name', true],
      ['email@domain.co.jp', true],
      ['firstname-lastname@domain.com', true],
      ['#@%^%#$@#$@#.com', false],
      ['@domain.com', false],
      ['Joe Smith <email@domain.com>', false],
      ['email.domain.com', false],
      ['email@domain@domain.com', false],
      ['.email@domain.com', false],
      ['email.@domain.com', false],
      ['email..email@domain.com', false],
      ['あいうえお@domain.com', true],
      ['email@domain.com (Joe Smith)', false],
      ['email@domain', true],
      ['email@-domain.com', false],
      ['email@domain.web', true],
      ['email@111.222.333.44444', true],
      ['email@domain..com', false],
    ];
  }
}