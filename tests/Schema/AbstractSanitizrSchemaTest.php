<?php

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;
use PHPUnit\Framework\TestCase;

class AbstractSanitizrSchemaTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testOptional(): void
    {
        $schema = (new SanitizrString())->optional();
        $this->assertEquals('test', $schema->parse('test'));
        // Optional value check would depend on how missing keys are handled in Object schema for example,
        // but simple standalone parse of null should not throw if nullable
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNullable(): void
    {
        $schema = (new SanitizrString())->nullable();
        $this->assertEquals('test', $schema->parse('test'));
        $this->assertNull($schema->parse(null));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNullish(): void
    {
        $schema = (new SanitizrString())->nullish();
        $this->assertEquals('test', $schema->parse('test'));
        $this->assertNull($schema->parse(null));
    }
}
