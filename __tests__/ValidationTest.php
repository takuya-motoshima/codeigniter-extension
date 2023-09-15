<?php
use PHPUnit\Framework\TestCase;
use \X\Util\Validation;

final class ValidationTest extends TestCase {
  /**
   * @dataProvider emailProvider
   */
  public function testEmail(string $value, bool $expected): void {
    $this->assertSame(Validation::email($value), $expected);
  }

  public function emailProvider(): array {
    return [
      // Passing case.
      ['email@domain.com', true],
      ['firstname.lastname@domain.com', true],
      ['email@subdomain.domain.com', true],
      ['firstname+lastname@domain.com', true],
      ['email@123.123.123.123', true],
      ['“email”@domain.com', true],
      ['1234567890@domain.com', true],
      ['email@domain-one.com', true],
      ['_______@domain.com', true],
      ['email@domain.name', true],
      ['email@domain.co.jp', true],
      ['firstname-lastname@domain.com', true],
      ['あいうえお@domain.com', true],
      ['email@domain', true],
      ['email@domain.web', true],
      ['email@111.222.333.44444', true],
      ['email@[123.123.123.123]', false],
      ['#@%^%#$@#$@#.com', false],
      ['@domain.com', false],

      // Rejected Cases.
      ['Joe Smith <email@domain.com>', false],
      ['email.domain.com', false],
      ['email@domain@domain.com', false],
      ['.email@domain.com', false],
      ['email.@domain.com', false],
      ['email..email@domain.com', false],
      ['email@domain.com (Joe Smith)', false],
      ['email@-domain.com', false],
      ['email@domain..com', false],
      ['email@domain..com', false],
    ];
  }

  /**
   * @dataProvider isPathProvider
   */
  public function testIsPath(string $value, bool $expected): void {
    $this->assertSame(Validation::is_path($value), $expected);
  }

  public function isPathProvider(): array {
    return [
      ['/', true],
      ['/usr', true],
      ['/usr/lib', true],
      ['/usr/lib/sysctl.d', true],
      ['/usr/lib/yum-plugins', true],
      ['/usr/lib/node_modules', true],
      ['/usr/123', true],

      // With leading and trailing slashes.
      ['/usr/', true],
      ['/usr/lib/', true],
      ['/usr/lib/sysctl.d/', true],
      ['/usr/lib/yum-plugins/', true],
      ['/usr/lib/node_modules/', true],
      ['/usr/123/', true],

      // Without leading slash, with trailing slash.
      ['usr/', true],
      ['usr/lib/', true],
      ['usr/lib/sysctl.d/', true],
      ['usr/lib/yum-plugins/', true],
      ['usr/lib/node_modules/', true],
      ['usr/123/', true],

      // Rejected Cases.
      ['//', false],
      ['//usr', false],
      ['/usr//', false],
      ['/usr//lib', false],
      ['/ドキュメント', false],
      ['/usr/ドキュメント', false],
    ];
  }
}