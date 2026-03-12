<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\SanitizrLiteral;
use PHPUnit\Framework\TestCase;

class SanitizrLiteralTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccessSingleLiteral(): void
    {
        $schema = new SanitizrLiteral('expected_value');
        $this->assertEquals('expected_value', $schema->parse('expected_value'));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccessArrayLiteral(): void
    {
        $schema = new SanitizrLiteral(['val1', 'val2']);
        $this->assertEquals('val1', $schema->parse('val1'));
        $this->assertEquals('val2', $schema->parse('val2'));
    }

    public function testValidationFailureSingleLiteral(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrLiteral('expected_value');
        $schema->parse('wrong_value');
    }

    public function testValidationFailureArrayLiteral(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrLiteral(['val1', 'val2']);
        $schema->parse('wrong_value');
    }

    public function testGetLiteralValue(): void
    {
        $schema = new SanitizrLiteral('test');
        $this->assertEquals('test', $schema->getLiteralValue());
    }
}
