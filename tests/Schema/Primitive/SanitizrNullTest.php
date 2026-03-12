<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\SanitizrNull;
use PHPUnit\Framework\TestCase;

class SanitizrNullTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccess(): void
    {
        $schema = new SanitizrNull();
        $this->assertNull($schema->parse(null));
    }

    public function testValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrNull();
        $schema->parse('not_null');
    }
}
