<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrBoolean;
use PHPUnit\Framework\TestCase;

class SanitizrBooleanTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccess(): void
    {
        $schema = new SanitizrBoolean();

        $this->assertTrue($schema->parse(true));
        $this->assertFalse($schema->parse(false));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testStringableSuccess(): void
    {
        $schema = (new SanitizrBoolean())->stringable();

        $this->assertTrue($schema->parse('true'));
        $this->assertTrue($schema->parse('1'));
        $this->assertTrue($schema->parse('on'));
        $this->assertTrue($schema->parse('yes'));

        $this->assertFalse($schema->parse('false'));
        $this->assertFalse($schema->parse('0'));
        $this->assertFalse($schema->parse('off'));
        $this->assertFalse($schema->parse('no'));
    }

    public function testValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrBoolean();
        $schema->parse('not_a_boolean');
    }
}
