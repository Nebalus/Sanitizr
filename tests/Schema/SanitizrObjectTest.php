<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
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
}
