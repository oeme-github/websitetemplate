<?php
declare(strict_types=1);

namespace Tests;

use App\Security\IbanValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class IbanValidatorTest extends TestCase
{
    private IbanValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new IbanValidator();
    }

    #[DataProvider('validIbanProvider')]
    public function testValidIbans(string $iban): void
    {
        $this->assertTrue(
            $this->validator->isValid($iban),
            "IBAN '$iban' should be valid"
        );
    }

    #[DataProvider('invalidIbanProvider')]
    public function testInvalidIbans(string $iban): void
    {
        $this->assertFalse(
            $this->validator->isValid($iban),
            "IBAN '$iban' should be invalid"
        );
    }

    public function testIbanWithSpaces(): void
    {
        $this->assertTrue($this->validator->isValid('DE89 3704 0044 0532 0130 00'));
    }

    public function testIbanCaseInsensitive(): void
    {
        $this->assertTrue($this->validator->isValid('de89370400440532013000'));
    }

    public static function validIbanProvider(): array
    {
        return [
            'German IBAN'   => ['DE89370400440532013000'],
            'Austrian IBAN' => ['AT611904300234573201'],
            'Swiss IBAN'    => ['CH9300762011623852957'],
            'Dutch IBAN'    => ['NL91ABNA0417164300'],
            'Belgian IBAN'  => ['BE68539007547034'],
        ];
    }

    public static function invalidIbanProvider(): array
    {
        return [
            'Too short'               => ['DE123'],
            'Invalid checksum'        => ['DE00123456789012345678'],
            'Wrong checksum digit'    => ['DE89370400440532013001'],
            'Invalid country'         => ['XX89370400440532013000'],
            'Empty string'            => [''],
            'Invalid characters'      => ['DE8937040044053201300!'],
            'Wrong length for country'=> ['DE8937040044053201300'],
        ];
    }
}
