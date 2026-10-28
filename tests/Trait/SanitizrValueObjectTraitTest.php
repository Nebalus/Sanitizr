<?php

declare(strict_types=1);

namespace UnitTesting\Trait;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\SanitizrValueObject;
use PHPUnit\Framework\TestCase;
use UnitTesting\Trait\Fixture\Address;
use UnitTesting\Trait\Fixture\BuildCounter;
use UnitTesting\Trait\Fixture\FlatEmail;
use UnitTesting\Trait\Fixture\Id;
use UnitTesting\Trait\Fixture\LowerCaseValue;
use UnitTesting\Trait\Fixture\OrderId;
use UnitTesting\Trait\Fixture\Point;
use UnitTesting\Trait\Fixture\UpperCaseValue;
use UnitTesting\Trait\Fixture\UserId;

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

    private const string UUID = 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa';

    public function testFromParsesIntoAnInstance(): void
    {
        $id = UserId::from(strtoupper(self::UUID));

        $this->assertInstanceOf(UserId::class, $id);
        $this->assertSame(self::UUID, $id->asString());
    }

    public function testFromThrowsForAnInvalidInput(): void
    {
        $this->expectException(SanitizrValidationException::class);
        UserId::from('not-a-uuid');
    }

    public function testTryFromAnswersNullForAnInvalidInput(): void
    {
        $this->assertNull(UserId::tryFrom('not-a-uuid'));
        $this->assertInstanceOf(UserId::class, UserId::tryFrom(self::UUID));
    }

    /** The constructor is private on the fixture: the trait builds from inside the class. */
    public function testFromReachesAPrivateConstructor(): void
    {
        $this->assertInstanceOf(Point::class, Point::from(['x' => 1, 'y' => 2]));
    }

    public function testFromParsedCanBeOverriddenForANonScalarSchema(): void
    {
        $point = Point::from(['x' => 3, 'y' => 4]);

        $this->assertSame(3, $point->x);
        $this->assertSame(4, $point->y);
    }

    public function testGetValueObjectSchemaIsTaggedWithTheConcreteClass(): void
    {
        $this->assertInstanceOf(SanitizrValueObject::class, UserId::getValueObjectSchema());
        $this->assertSame(UserId::class, UserId::getValueObjectSchema()->getClass());
        $this->assertSame(OrderId::class, OrderId::getValueObjectSchema()->getClass());
    }

    /** Two ids over the same base schema must still come out as their own class. */
    public function testEachSubclassBuildsItself(): void
    {
        $this->assertInstanceOf(UserId::class, UserId::from(self::UUID));
        $this->assertInstanceOf(OrderId::class, OrderId::from(self::UUID));
        $this->assertNotInstanceOf(OrderId::class, UserId::from(self::UUID));
    }

    /** isValid() runs the plain schema, so it still answers on the abstract base. */
    public function testIsValidWorksOnTheAbstractBase(): void
    {
        $this->assertTrue(Id::isValid(self::UUID));
        $this->assertFalse(Id::isValid('not-a-uuid'));
    }

    public function testGetSchemaStillYieldsThePlainValue(): void
    {
        $this->assertSame(self::UUID, UserId::getSchema()->parse(strtoupper(self::UUID)));
    }
}
