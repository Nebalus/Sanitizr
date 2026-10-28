<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use PHPUnit\Framework\TestCase;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;
use Nebalus\Sanitizr\Schema\SanitizrObject;

class SanitizrObjectTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testTransform(): void
    {
        $schema = new SanitizrObject([
            'name' => new SanitizrString(),
            'age' => (new SanitizrString())->digits()->transform(fn($a) => (int)$a)
        ]);

        $schema = $schema->transform(function (array $data) {
            return (object) $data;
        });

        $result = $schema->parse([
            'name' => 'John',
            'age' => '30'
        ]);

        $this->assertIsObject($result);
        $this->assertSame('John', $result->name);
        $this->assertSame(30, $result->age);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNestedObjectTransformIsApplied(): void
    {
        $schema = S::object([
            'user' => S::object(['name' => S::string()])->transform(fn(array $data) => (object) $data),
        ]);

        $result = $schema->parse(['user' => ['name' => 'John']]);

        $this->assertIsObject($result['user']);
        $this->assertSame('John', $result['user']->name);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNestedObjectCanBeNullable(): void
    {
        $schema = S::object([
            'user' => S::object(['name' => S::string()])->nullable(),
        ]);

        $this->assertSame(['user' => null], $schema->parse(['user' => null]));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNestedObjectDefaultIsApplied(): void
    {
        $schema = S::object([
            'user' => S::object(['name' => S::string()])->default(['name' => 'Guest']),
        ]);

        $this->assertSame(['user' => ['name' => 'Guest']], $schema->parse(['user' => null]));
        $this->assertSame(['user' => ['name' => 'Guest']], $schema->parse([]));
    }

    public function testNestedObjectChecksAreApplied(): void
    {
        $schema = S::object([
            'user' => S::object(['name' => S::string()])->and(S::object(['name' => S::string()->min(3)])),
        ]);

        $this->assertFalse($schema->safeParse(['user' => ['name' => 'Jo']])->isValid());
        $this->assertTrue($schema->safeParse(['user' => ['name' => 'John']])->isValid());
    }

    public function testNestedObjectErrorKeepsItsPath(): void
    {
        $schema = S::object([
            'user' => S::object([
                'address' => S::object(['zip' => S::string()->digits()]),
            ]),
        ]);

        $error = $schema->safeParse(['user' => ['address' => ['zip' => 'abc']]])->getError();

        $this->assertSame('user.address.zip', $error->getIssues()[0]->getPathString());
    }
}
