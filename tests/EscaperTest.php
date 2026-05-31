<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class EscaperTest extends TestCase
{
    #[DataProvider('escapeProvider')]
    public function testEscapeFunction(string $input, string $expected): void
    {
        $this->assertSame($expected, e($input));
    }

    public static function escapeProvider(): array
    {
        return [
            'normal text'    => ['Hello World', 'Hello World'],
            'HTML tags'      => ['<div>test</div>', '&lt;div&gt;test&lt;/div&gt;'],
            'script tag'     => ['<script>alert("xss")</script>', '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'],
            'ampersand'      => ['Tom & Jerry', 'Tom &amp; Jerry'],
            'double quotes'  => ['"quoted"', '&quot;quoted&quot;'],
            'single quotes'  => ["it's", "it&#039;s"],
            'empty string'   => ['', ''],
            'unicode'        => ['Über uns', 'Über uns'],
            'german umlauts' => ['äöüß', 'äöüß'],
            'nested HTML'    => ['<div onclick="alert(1)">click</div>', '&lt;div onclick=&quot;alert(1)&quot;&gt;click&lt;/div&gt;'],
        ];
    }
}
