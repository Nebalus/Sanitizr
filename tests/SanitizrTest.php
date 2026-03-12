<?php

declare(strict_types=1);

namespace UnitTesting;

use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrBoolean;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrNumber;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;
use Nebalus\Sanitizr\Schema\SanitizrArray;
use Nebalus\Sanitizr\Schema\SanitizrDiscriminatedUnion;
use Nebalus\Sanitizr\Schema\SanitizrLiteral;
use Nebalus\Sanitizr\Schema\SanitizrNull;
use Nebalus\Sanitizr\Schema\SanitizrObject;
use Nebalus\Sanitizr\Schema\SanitizrTuple;
use PHPUnit\Framework\TestCase;

class SanitizrTest extends TestCase
{
    private Sanitizr $sanitizr;

    protected function setUp(): void
    {
        $this->sanitizr = new Sanitizr();
    }

    public function testLiteral(): void
    {
        $this->assertInstanceOf(SanitizrLiteral::class, $this->sanitizr->literal('test'));
    }

    public function testBoolean(): void
    {
        $this->assertInstanceOf(SanitizrBoolean::class, $this->sanitizr->boolean());
    }

    public function testNumber(): void
    {
        $this->assertInstanceOf(SanitizrNumber::class, $this->sanitizr->number());
    }

    public function testString(): void
    {
        $this->assertInstanceOf(SanitizrString::class, $this->sanitizr->string());
    }

    public function testFloat(): void
    {
        $this->assertInstanceOf(SanitizrNumber::class, $this->sanitizr->float());
    }

    public function testInteger(): void
    {
        $this->assertInstanceOf(SanitizrNumber::class, $this->sanitizr->integer());
    }

    public function testArray(): void
    {
        $this->assertInstanceOf(SanitizrArray::class, $this->sanitizr->array($this->sanitizr->string()));
    }

    public function testObject(): void
    {
        $this->assertInstanceOf(SanitizrObject::class, $this->sanitizr->object([]));
    }

    public function testTuple(): void
    {
        $this->assertInstanceOf(SanitizrTuple::class, $this->sanitizr->tuple($this->sanitizr->string()));
    }

    public function testNull(): void
    {
        $this->assertInstanceOf(SanitizrNull::class, $this->sanitizr->null());
    }

    public function testNullable(): void
    {
        $schema = $this->sanitizr->nullable($this->sanitizr->string());
        $this->assertTrue($schema->isNullable());
    }

    public function testNullish(): void
    {
        $schema = $this->sanitizr->nullish($this->sanitizr->string());
        $this->assertTrue($schema->isNullable());
        $this->assertTrue($schema->isOptional());
    }

    public function testOptional(): void
    {
        $schema = $this->sanitizr->optional($this->sanitizr->string());
        $this->assertTrue($schema->isOptional());
    }

    public function testNonOptional(): void
    {
        $schema = $this->sanitizr->nonOptional($this->sanitizr->string()->optional());
        $this->assertFalse($schema->isOptional());
    }

    public function testDiscriminatedUnion(): void
    {
        $this->assertInstanceOf(
            SanitizrDiscriminatedUnion::class,
            $this->sanitizr->discriminatedUnion('type', $this->sanitizr->object(['type' => $this->sanitizr->literal('a')]))
        );
    }
}
