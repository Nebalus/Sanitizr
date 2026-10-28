<?php

declare(strict_types=1);

namespace UnitTesting\Trait;

use Nebalus\Sanitizr\SanitizrStatic as S;
use PHPUnit\Framework\TestCase;
use UnitTesting\Trait\Fixture\Address;
use UnitTesting\Trait\Fixture\BuildCounter;
use UnitTesting\Trait\Fixture\FlatEmail;
use UnitTesting\Trait\Fixture\LowerCaseValue;
use UnitTesting\Trait\Fixture\UpperCaseValue;

class SanitizrValueObjectTraitTest extends TestCase
{
    public function testGetSchemaReturnsAClone(): void
    {
        $this->assertNotSame(FlatEmail::getSchema(), FlatEmail::getSchema());
    }

    public function testIsValidReportsInputValidity(): void
    {
        $this->assertTrue(FlatEmail::isValid('user@example.com'));
        $this->assertFalse(FlatEmail::isValid('not an email'));
    }

    public function testSchemaIsBuiltOnce(): void
    {
        BuildCounter::$builds = 0;

        BuildCounter::getSchema();
        BuildCounter::getSchema();
        BuildCounter::isValid('x');

        $this->assertSame(1, BuildCounter::$builds);
    }

    /**
     * The trait is used by an abstract base class here and each subclass
     * defines its own schema. The base class's `defineSchema()` is abstract, so
     * resolving it with `self::` would fail, and a single cache slot would hand
     * the first subclass's schema to every other subclass.
     */
    public function testEachSubclassGetsItsOwnSchema(): void
    {
        $this->assertTrue(LowerCaseValue::isValid('abc'));
        $this->assertFalse(LowerCaseValue::isValid('ABC'));

        $this->assertTrue(UpperCaseValue::isValid('ABC'));
        $this->assertFalse(UpperCaseValue::isValid('abc'));

        // Touched in the opposite order, the first one still answers for itself.
        $this->assertFalse(LowerCaseValue::isValid('ABC'));
        $this->assertTrue(LowerCaseValue::isValid('abc'));
    }

    public function testSubclassSchemasAreCachedSeparately(): void
    {
        $this->assertNotEquals(LowerCaseValue::getSchema(), UpperCaseValue::getSchema());
    }

    /**
     * An object schema used as a property of another object schema must go
     * through the full parse, or the transform that builds the instance is skipped.
     */
    public function testObjectSchemaAsPropertyYieldsTheInstance(): void
    {
        $result = S::object(['address' => Address::getSchema()])
            ->parse(['address' => ['city' => 'Berlin', 'zip' => '10115']]);

        $this->assertInstanceOf(Address::class, $result['address']);
        $this->assertSame('Berlin', $result['address']->city);
    }

    public function testObjectSchemaInsideUnionYieldsTheInstance(): void
    {
        $result = S::object(['address' => S::union(Address::getSchema(), S::string())])
            ->parse(['address' => ['city' => 'Berlin', 'zip' => '10115']]);

        $this->assertInstanceOf(Address::class, $result['address']);
    }
}
