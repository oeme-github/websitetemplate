<?php
declare(strict_types=1);

namespace Tests;

use App\Helpers\Helpers;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class HelpersTest extends TestCase
{
    #[DataProvider('cleanProvider')]
    public function testClean(string $input, string $expected): void
    {
        $this->assertSame($expected, Helpers::clean($input));
    }

    public static function cleanProvider(): array
    {
        return [
            'normal text'   => ['Hello World', 'Hello World'],
            'with spaces'   => ['  Hello  ', 'Hello'],
            'HTML tags'     => ['<script>alert("xss")</script>', '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'],
            'special chars' => ['Tom & Jerry', 'Tom &amp; Jerry'],
            'quotes'        => ['"quoted"', '&quot;quoted&quot;'],
            'single quotes' => ["it's", "it&#039;s"],
            'empty string'  => ['', ''],
            'unicode'       => ['Über uns', 'Über uns'],
            'german umlauts'=> ['äöüß', 'äöüß'],
        ];
    }

    #[DataProvider('maskIbanProvider')]
    public function testMaskIban(string $iban, string $expected): void
    {
        $this->assertSame($expected, Helpers::maskIBAN($iban));
    }

    public static function maskIbanProvider(): array
    {
        return [
            'German IBAN'   => ['DE89370400440532013000', 'DE89 **** **** 3000'],
            'with spaces'   => ['DE89 3704 0044 0532 0130 00', 'DE89 **** **** 3000'],
            'Austrian IBAN' => ['AT611904300234573201', 'AT61 **** **** 3201'],
        ];
    }

    public function testIsDevReturnsTrueInDevEnvironment(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue(Helpers::is_dev());
    }

    public function testIsDevReturnsFalseInProdEnvironment(): void
    {
        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse(Helpers::is_dev());
    }

    public function testIsDevReturnsFalseWhenNotSet(): void
    {
        unset($_ENV['APP_ENV']);
        $this->assertFalse(Helpers::is_dev());
    }
}
