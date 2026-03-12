<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;
use Nebalus\Sanitizr\Schema\SanitizrArray;
use PHPUnit\Framework\TestCase;

class SanitizrArrayTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccess(): void
    {
        $stringSchema = clone new SanitizrString();
        $schema = new SanitizrArray($stringSchema);

        $this->assertEquals(['a', 'b', 'c'], $schema->parse(['a', 'b', 'c']));
        $this->assertEquals([], $schema->parse([]));
    }

    public function testValidationFailureMismatchType(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Value must be an ARRAY');

        $stringSchema = clone new SanitizrString();
        $schema = new SanitizrArray($stringSchema);

        $schema->parse('not_an_array');
    }

    public function testValidationFailureInnerElement(): void
    {
        $this->expectException(SanitizrValidationException::class);

        $stringSchema = clone new SanitizrString();
        $schema = new SanitizrArray($stringSchema);

        $schema->parse(['a', 'b', 123]); // 123 is not a string
    }
}
