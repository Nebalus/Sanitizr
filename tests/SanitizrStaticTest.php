<?php

declare(strict_types=1);

namespace UnitTesting;

use Nebalus\Sanitizr\SanitizrStatic as S;
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

class SanitizrStaticTest extends TestCase
{
    public function testLiteral(): void
    {
        $this->assertInstanceOf(SanitizrLiteral::class, S::literal('test'));
    }

    public function testBoolean(): void
    {
        $this->assertInstanceOf(SanitizrBoolean::class, S::boolean());
    }

    public function testNumber(): void
    {
        $this->assertInstanceOf(SanitizrNumber::class, S::number());
    }

    public function testString(): void
    {
        $this->assertInstanceOf(SanitizrString::class, S::string());
    }

    public function testFloat(): void
    {
        $this->assertInstanceOf(SanitizrNumber::class, S::float());
    }

    public function testInteger(): void
    {
        $this->assertInstanceOf(SanitizrNumber::class, S::integer());
    }

    public function testArray(): void
    {
        $this->assertInstanceOf(SanitizrArray::class, S::array(S::string()));
    }

    public function testObject(): void
    {
        $this->assertInstanceOf(SanitizrObject::class, S::object([]));
    }

    public function testTuple(): void
    {
        $this->assertInstanceOf(SanitizrTuple::class, S::tuple(S::string()));
    }

    public function testNull(): void
    {
        $this->assertInstanceOf(SanitizrNull::class, S::null());
    }

    public function testNullable(): void
    {
        $schema = S::nullable(S::string());
        $this->assertTrue($schema->isNullable());
    }

    public function testNullish(): void
    {
        $schema = S::nullish(S::string());
        $this->assertTrue($schema->isNullable());
        $this->assertTrue($schema->isOptional());
    }

    public function testOptional(): void
    {
        $schema = S::optional(S::string());
        $this->assertTrue($schema->isOptional());
    }

    public function testNonOptional(): void
    {
        $schema = S::nonOptional(S::string()->optional());
        $this->assertFalse($schema->isOptional());
    }

    public function testDiscriminatedUnion(): void
    {
        $this->assertInstanceOf(
            SanitizrDiscriminatedUnion::class,
            S::discriminatedUnion('type', S::object(['type' => S::literal('a')]))
        );
    }
}
