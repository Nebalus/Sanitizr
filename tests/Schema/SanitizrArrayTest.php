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
    public function testValidationMin(): void
    {
        $schema = clone new SanitizrArray(new SanitizrString());
        $schema = $schema->min(2);
        
        $this->assertEquals(['a', 'b'], $schema->parse(['a', 'b']));
        $this->assertEquals(['a', 'b', 'c'], $schema->parse(['a', 'b', 'c']));

        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Array must contain at least 2 element(s)');
        $schema->parse(['a']);
    }

    public function testValidationMax(): void
    {
        $schema = clone new SanitizrArray(new SanitizrString());
        $schema = $schema->max(2);
        
        $this->assertEquals(['a', 'b'], $schema->parse(['a', 'b']));
        $this->assertEquals(['a'], $schema->parse(['a']));

        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Array must contain at most 2 element(s)');
        $schema->parse(['a', 'b', 'c']);
    }

    public function testValidationBetween(): void
    {
        $schema = clone new SanitizrArray(new SanitizrString());
        $schema = $schema->between(2, 4);
        
        $this->assertEquals(['a', 'b'], $schema->parse(['a', 'b']));
        $this->assertEquals(['a', 'b', 'c', 'd'], $schema->parse(['a', 'b', 'c', 'd']));

        try {
            $schema->parse(['a']);
            $this->fail('Expected SanitizrValidationException for too small array');
        } catch (SanitizrValidationException $e) {
            $this->assertStringContainsString('Array must contain between 2 and 4 element(s)', $e->getMessage());
        }

        try {
            $schema->parse(['a', 'b', 'c', 'd', 'e']);
            $this->fail('Expected SanitizrValidationException for too large array');
        } catch (SanitizrValidationException $e) {
            $this->assertStringContainsString('Array must contain between 2 and 4 element(s)', $e->getMessage());
        }
    }

    public function testValidationNotEmpty(): void
    {
        $schema = clone new SanitizrArray(new SanitizrString());
        $schema = $schema->notEmpty();
        
        $this->assertEquals(['a'], $schema->parse(['a']));

        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Array cannot be empty');
        $schema->parse([]);
    }

    public function testValidationEmpty(): void
    {
        $schema = clone new SanitizrArray(new SanitizrString());
        $schema = $schema->empty();
        
        $this->assertEquals([], $schema->parse([]));

        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Array must be empty');
        $schema->parse(['a']);
    }
}
