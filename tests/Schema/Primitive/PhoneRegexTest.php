<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class PhoneRegexTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testPhoneWithDigitsSuccess(): void
    {
        $schema = (new SanitizrString())->phone();

        $this->assertSame('+1234567890', $schema->parse('+1234567890'));
        $this->assertSame('123-456-7890', $schema->parse('123-456-7890'));
        $this->assertSame('(123) 4567890', $schema->parse('(123) 4567890'));
        $this->assertSame('1234567', $schema->parse('1234567'));
    }

    #[DataProvider('invalidPhoneProvider')]
    public function testPhoneValidationFailure(string $input): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->phone();
        $schema->parse($input);
    }

    public static function invalidPhoneProvider(): array
    {
        return [
            ['+ ( ) -   '],
            ['-------'],
            ['123456'],
            ['123456789012345678901'],
            ['123-456-789a'],
        ];
    }
}
