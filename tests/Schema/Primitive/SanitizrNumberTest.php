<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrNumber;
use PHPUnit\Framework\TestCase;

class SanitizrNumberTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccess(): void
    {
        $schema = new SanitizrNumber();

        $this->assertEquals(123, $schema->parse(123));
        $this->assertEquals(123.45, $schema->parse(123.45));
    }

    public function testValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrNumber();
        $schema->parse('not_a_number');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testStringableNumber(): void
    {
        $schema = clone (new SanitizrNumber())->stringable();
        $this->assertEquals(123, $schema->parse('123'));
        $this->assertEquals(123.45, $schema->parse('123.45'));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testGreaterThan(): void
    {
        $schema = clone (new SanitizrNumber())->gt(10);
        $this->assertEquals(11, $schema->parse(11));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(9);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testGreaterThanOrEqual(): void
    {
        $schema = clone (new SanitizrNumber())->gte(10);
        $this->assertEquals(10, $schema->parse(10));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(9);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testLessThan(): void
    {
        $schema = clone (new SanitizrNumber())->lt(10);
        $this->assertEquals(9, $schema->parse(9));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(11);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testLessThanOrEqual(): void
    {
        $schema = clone (new SanitizrNumber())->lte(10);
        $this->assertEquals(10, $schema->parse(10));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(11);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testFloat(): void
    {
        $schema = clone (new SanitizrNumber())->float();
        $this->assertEquals(10.5, $schema->parse(10.5));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(10);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testInteger(): void
    {
        $schema = clone (new SanitizrNumber())->integer();
        $this->assertEquals(10, $schema->parse(10));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(10.5);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testPositive(): void
    {
        $schema = clone (new SanitizrNumber())->positive();
        $this->assertEquals(1, $schema->parse(1));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(0);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNonPositive(): void
    {
        $schema = clone (new SanitizrNumber())->nonPositive();
        $this->assertEquals(0, $schema->parse(0));
        $this->assertEquals(-1, $schema->parse(-1));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(1);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNegative(): void
    {
        $schema = clone (new SanitizrNumber())->negative();
        $this->assertEquals(-1, $schema->parse(-1));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(0);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNonNegative(): void
    {
        $schema = clone (new SanitizrNumber())->nonNegative();
        $this->assertEquals(0, $schema->parse(0));
        $this->assertEquals(1, $schema->parse(1));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(-1);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testMultipleOf(): void
    {
        $schema = clone (new SanitizrNumber())->multipleOf(5);
        $this->assertEquals(10, $schema->parse(10));

        $this->expectException(SanitizrValidationException::class);
        $schema->parse(7);
    }
}
