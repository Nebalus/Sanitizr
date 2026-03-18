<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use InvalidArgumentException;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use PHPUnit\Framework\TestCase;

class SanitizrUnionTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testUnionRoutingSuccess(): void
    {
        $schema = S::union(
            S::string(),
            S::number(),
            S::object([
                'type' => S::literal('A'),
                'valueA' => S::string()
            ])
        );

        $inputStr = 'test_string';
        $inputNum = 123;
        $inputObj = ['type' => 'A', 'valueA' => 'test'];

        $this->assertSame($inputStr, $schema->parse($inputStr));
        $this->assertSame($inputNum, $schema->parse($inputNum));
        $this->assertSame($inputObj, $schema->parse($inputObj));
    }

    public function testUnionValidationFailureOnInvalidType(): void
    {
        $this->expectException(SanitizrValidationException::class);

        $schema = clone S::union(
            S::string(),
            S::number()
        );

        $schema->parse(['array_is_not_string_or_number']);
    }

    public function testEmptyOptionsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        S::union();
    }
}
