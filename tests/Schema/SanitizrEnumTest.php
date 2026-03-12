<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\SanitizrEnum;
use PHPUnit\Framework\TestCase;

class SanitizrEnumTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccess(): void
    {
        $schema = new SanitizrEnum(['active', 'inactive', 'pending']);
        $this->assertEquals('active', $schema->parse('active'));
        $this->assertEquals('inactive', $schema->parse('inactive'));
        $this->assertEquals('pending', $schema->parse('pending'));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testValidationSuccessWithMixedTypes(): void
    {
        $schema = new SanitizrEnum(['a', 1, true]);
        $this->assertEquals('a', $schema->parse('a'));
        $this->assertEquals(1, $schema->parse(1));
        $this->assertEquals(true, $schema->parse(true));
    }

    public function testValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Value must be one of: [active, inactive]');

        $schema = new SanitizrEnum(['active', 'inactive']);
        $schema->parse('deleted');
    }

    public function testValidationFailureWithMixedTypesMessage(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $this->expectExceptionMessage('Value must be one of: [a, 1, 1]'); // true maps to 1 in string conversion usually, but let's see my implementation

        $schema = new SanitizrEnum(['a', 1, true]);
        $schema->parse('wrong');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNullableEnum(): void
    {
        $schema = (new SanitizrEnum(['a', 'b']))->nullable();
        $this->assertNull($schema->parse(null));
        $this->assertEquals('a', $schema->parse('a'));
    }

    public function testGetValues(): void
    {
        $values = ['red', 'green', 'blue'];
        $schema = new SanitizrEnum($values);
        $this->assertEquals($values, $schema->getValues());
    }
}
