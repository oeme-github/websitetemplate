<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class RateLimitTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testFirstAttemptIsAllowed(): void
    {
        $bucket = [];
        $result = rateLimitCheck($bucket, 1000, 5, 900);

        $this->assertTrue($result);
        $this->assertSame(1, $bucket['count']);
    }

    public function testAttemptsUpToLimitAreAllowed(): void
    {
        $bucket = [];

        for ($i = 1; $i <= 5; $i++) {
            $result = rateLimitCheck($bucket, 1000, 5, 900);
            $this->assertTrue($result, "Attempt $i should be allowed");
        }

        $this->assertSame(5, $bucket['count']);
    }

    public function testAttemptBeyondLimitIsBlocked(): void
    {
        $bucket = [];

        for ($i = 0; $i < 5; $i++) {
            rateLimitCheck($bucket, 1000, 5, 900);
        }

        $result = rateLimitCheck($bucket, 1000, 5, 900);

        $this->assertFalse($result);
        $this->assertSame(6, $bucket['count']);
    }

    public function testExpiredWindowResetsCounter(): void
    {
        $bucket = [];
        $start  = 1000;

        for ($i = 0; $i < 5; $i++) {
            rateLimitCheck($bucket, $start, 5, 900);
        }

        // Window has fully elapsed
        $result = rateLimitCheck($bucket, $start + 900, 5, 900);

        $this->assertTrue($result);
        $this->assertSame(1, $bucket['count']);
        $this->assertSame($start + 900, $bucket['since']);
    }

    public function testWindowNotYetExpiredKeepsCounter(): void
    {
        $bucket = [];
        $start  = 1000;

        rateLimitCheck($bucket, $start, 5, 900);
        rateLimitCheck($bucket, $start + 899, 5, 900);

        $this->assertSame(2, $bucket['count']);
        $this->assertSame($start, $bucket['since']);
    }

    public function testBucketSinceIsSetOnFirstCall(): void
    {
        $bucket = [];
        rateLimitCheck($bucket, 4242, 5, 900);

        $this->assertSame(4242, $bucket['since']);
    }

    public function testEmptyBucketIsInitializedCorrectly(): void
    {
        $bucket = [];
        rateLimitCheck($bucket, 1000, 5, 900);

        $this->assertArrayHasKey('count', $bucket);
        $this->assertArrayHasKey('since', $bucket);
    }
}
