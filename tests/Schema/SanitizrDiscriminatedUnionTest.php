<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use InvalidArgumentException;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use PHPUnit\Framework\TestCase;

class SanitizrDiscriminatedUnionTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testDiscriminatorRoutingSuccess(): void
    {
        $schema = S::discriminatedUnion(
            'type',
            S::object([
                'type' => S::literal('A'),
                'valueA' => S::string()
            ]),
            S::object([
                'type' => S::literal('B'),
                'valueB' => S::number()
            ])
        );

        $inputA = ['type' => 'A', 'valueA' => 'test'];
        $inputB = ['type' => 'B', 'valueB' => 123];

        $this->assertSame($inputA, $schema->parse($inputA));
        $this->assertSame($inputB, $schema->parse($inputB));
    }

    public function testDiscriminatorValidationFailureOnMissingKey(): void
    {
        $this->expectException(SanitizrValidationException::class);

        $schema = clone S::discriminatedUnion(
            'type',
            S::object([
                'type' => S::literal('A'),
                'valueA' => S::string()
            ])
        );

        $schema->parse(['valueA' => 'test']); // Missing "type" discriminator key
    }

    public function testDiscriminatorValidationFailureOnInvalidType(): void
    {
        $this->expectException(SanitizrValidationException::class);

        $schema = clone S::discriminatedUnion(
            'type',
            S::object([
                'type' => S::literal('A'),
                'valueA' => S::string()
            ])
        );

        $schema->parse(['type' => 'Unknown', 'valueA' => 'test']);
    }

    public function testInvalidDiscriminatorValuesThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        S::discriminatedUnion(
            'type',
            S::object([
                'type' => S::string(), // Discriminator must be a literal
                'valueA' => S::string()
            ])
        );
    }

    public function testEmptyOptionsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        S::discriminatedUnion('type');
    }
}
