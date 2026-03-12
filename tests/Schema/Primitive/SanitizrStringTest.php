<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use PHPUnit\Framework\TestCase;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;

class SanitizrStringTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testPhoneValidationSuccess(): void
    {
        $schema = new SanitizrString();
        $schema = $schema->phone();

        $this->assertSame('+1234567890', $schema->parse('+1234567890'));
        $this->assertSame('123-456-7890', $schema->parse('123-456-7890'));
        $this->assertSame('+44 (0) 20 1234 5678', $schema->parse('+44 (0) 20 1234 5678'));
    }

    public function testPhoneValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrString();
        $schema = $schema->phone();
        $schema->parse('not_a_phone_number');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testTransform(): void
    {
        $schema = (new SanitizrString())->email()->transform(function (string $input) {
            return (object) ['email' => $input];
        });

        $result = $schema->parse('test@example.com');
        $this->assertIsObject($result);
        $this->assertSame('test@example.com', $result->email);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testAlphanumericValidationSuccess(): void
    {
        $schema = (new SanitizrString())->alphanumeric();

        $this->assertSame('A1B2C3', $schema->parse('A1B2C3'));
        $this->assertSame('justLetters', $schema->parse('justLetters'));
        $this->assertSame('123456', $schema->parse('123456'));
    }

    public function testAlphanumericValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->alphanumeric();
        $schema->parse('not_alphanumeric_123!');
    }
}
