<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use LogicException;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\SanitizrValueObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use UnitTesting\Trait\Fixture\OrderId;
use UnitTesting\Trait\Fixture\Point;
use UnitTesting\Trait\Fixture\UserId;

class SanitizrValueObjectTest extends TestCase
{
    private const string UUID = 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa';

    public function testParseYieldsTheInstanceFromTheNormalisedValue(): void
    {
        $id = UserId::getValueObjectSchema()->parse('  ' . strtoupper(self::UUID) . '  ');

        $this->assertInstanceOf(UserId::class, $id);
        $this->assertSame(self::UUID, $id->asString());
    }

    public function testExposesTheClassAndTheInnerSchema(): void
    {
        $schema = UserId::getValueObjectSchema();

        $this->assertSame(UserId::class, $schema->getClass());
        $this->assertSame(self::UUID, $schema->getInnerSchema()->parse(self::UUID));
    }

    /**
     * The point of a schema node over a post-parse transform: the instance is
     * built wherever the node sits, not only at the top level.
     */
    public function testYieldsInstancesInsideObjectsArraysUnionsAndTuples(): void
    {
        $parsed = S::object([
            'user' => UserId::getValueObjectSchema(),
            'orders' => S::array(OrderId::getValueObjectSchema()),
            'either' => S::union(S::integer(), UserId::getValueObjectSchema()),
            'pair' => S::tuple(UserId::getValueObjectSchema(), Point::getValueObjectSchema()),
            'nested' => S::array(S::object(['id' => OrderId::getValueObjectSchema()])),
        ])->parse([
            'user' => self::UUID,
            'orders' => [self::UUID, self::UUID],
            'either' => self::UUID,
            'pair' => [self::UUID, ['x' => 1, 'y' => 2]],
            'nested' => [['id' => self::UUID]],
        ]);

        $this->assertInstanceOf(UserId::class, $parsed['user']);
        $this->assertContainsOnlyInstancesOf(OrderId::class, $parsed['orders']);
        $this->assertInstanceOf(UserId::class, $parsed['either']);
        $this->assertInstanceOf(UserId::class, $parsed['pair'][0]);
        $this->assertInstanceOf(Point::class, $parsed['pair'][1]);
        $this->assertSame(2, $parsed['pair'][1]->y);
        $this->assertInstanceOf(OrderId::class, $parsed['nested'][0]['id']);
    }

    public function testNullableOptionalAndDefaultAreAnsweredBeforeConstruction(): void
    {
        $parsed = S::object([
            'nullable' => UserId::getValueObjectSchema()->nullable(),
            'optional' => UserId::getValueObjectSchema()->optional(),
            'defaulted' => UserId::getValueObjectSchema()->default(null),
        ])->parse(['nullable' => null]);

        $this->assertNull($parsed['nullable']);
        $this->assertArrayNotHasKey('optional', $parsed);
        $this->assertNull($parsed['defaulted']);
    }

    public function testIssuesKeepThePathOfTheField(): void
    {
        try {
            S::object(['user' => S::object(['id' => UserId::getValueObjectSchema()])])
                ->parse(['user' => ['id' => 'not-a-uuid']]);
            $this->fail('expected SanitizrValidationException');
        } catch (SanitizrValidationException $e) {
            $this->assertSame('user.id', $e->getError()->getIssues()[0]->getPathString());
        }
    }

    public function testAFactoryReturningTheWrongClassIsAProgrammingError(): void
    {
        $schema = new SanitizrValueObject(UserId::class, S::string(), static fn(string $v): object => new stdClass());

        $this->expectException(LogicException::class);
        $schema->parse('x');
    }

    /** The factory has no inverse, so encoding through it is a programming error, as with a transform. */
    public function testCannotBeEncoded(): void
    {
        $schema = S::object(['id' => UserId::getValueObjectSchema()]);
        $parsed = $schema->parse(['id' => self::UUID]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(UserId::class);

        $schema->encode($parsed);
    }

    public function testNullIsStillEncodedWhenNullable(): void
    {
        $this->assertNull(UserId::getValueObjectSchema()->nullable()->encode(null));
    }
}
