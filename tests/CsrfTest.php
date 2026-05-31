<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testCsrfTokenGeneratesToken(): void
    {
        $token = csrf_token();

        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token));
    }

    public function testCsrfTokenReturnsSameTokenOnSubsequentCalls(): void
    {
        $token1 = csrf_token();
        $token2 = csrf_token();

        $this->assertSame($token1, $token2);
    }

    public function testCsrfVerifyReturnsTrueForValidToken(): void
    {
        $token = csrf_token();

        $this->assertTrue(csrf_verify($token));
    }

    public function testCsrfVerifyReturnsFalseForInvalidToken(): void
    {
        csrf_token();

        $this->assertFalse(csrf_verify('invalid_token'));
    }

    public function testCsrfVerifyReturnsFalseForNullToken(): void
    {
        csrf_token();

        $this->assertFalse(csrf_verify(null));
    }

    public function testCsrfVerifyReturnsFalseForEmptyToken(): void
    {
        csrf_token();

        $this->assertFalse(csrf_verify(''));
    }

    public function testCsrfVerifyReturnsFalseWhenNoSessionToken(): void
    {
        $this->assertFalse(csrf_verify('some_token'));
    }

    public function testCsrfRegenerateClearsToken(): void
    {
        $token1 = csrf_token();
        csrf_regenerate();
        $token2 = csrf_token();

        $this->assertNotSame($token1, $token2);
    }

    public function testCsrfTokenIsStoredInSession(): void
    {
        $token = csrf_token();

        $this->assertArrayHasKey('_csrf_token', $_SESSION);
        $this->assertSame($token, $_SESSION['_csrf_token']);
    }
}
