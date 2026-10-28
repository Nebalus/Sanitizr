<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use PHPUnit\Framework\TestCase;

class SanitizrCatchTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testValidInputIsReturnedUnchanged(): void
    {
        $this->assertSame('hello', S::string()->catch('fallback')->parse('hello'));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testInvalidInputReturnsTheCatchValue(): void
    {
        $this->assertSame('fallback', S::string()->catch('fallback')->parse(123));
        $this->assertSame(0, S::number()->positive()->catch(0)->parse(-5));
    }

    public function testSafeParseIsValidWithTheCatchValue(): void
    {
        $result = S::string()->email()->catch(null)->safeParse('not an email');

        $this->assertTrue($result->isValid());
        $this->assertNull($result->getValue());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testClosureReceivesTheErrorAndTheInput(): void
    {
        $schema = S::number()->catch(fn(SanitizrError $error, mixed $input) => [
            $error->getIssues()[0]->code,
            $input,
        ]);

        $this->assertSame([SanitizrIssue::INVALID_TYPE, 'abc'], $schema->parse('abc'));
    }

    /**
     * A callable string is a value like any other; only a Closure is called.
     *
     * @throws SanitizrValidationException
     */
    public function testCallableStringIsNotCalled(): void
    {
        $this->assertSame('strtoupper', S::string()->catch('strtoupper')->parse(123));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testCatchAppliesAfterTheOrFallbacks(): void
    {
        $schema = S::string()->or(S::number())->catch('fallback');

        $this->assertSame(42, $schema->parse(42));
        $this->assertSame('fallback', $schema->parse(true));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testInvalidPropertyIsCaughtInsideAnObject(): void
    {
        $schema = S::object([
            'name' => S::string(),
            'age' => S::integer()->catch(0),
        ]);

        $this->assertSame(['name' => 'John', 'age' => 0], $schema->parse(['name' => 'John', 'age' => 'abc']));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testMissingKeyIsCaughtInsideAnObject(): void
    {
        $schema = S::object([
            'name' => S::string(),
            'role' => S::string()->catch(fn(SanitizrError $error) => $error->getIssues()[0]->code),
        ]);

        $this->assertSame(['name' => 'John', 'role' => SanitizrIssue::MISSING_KEY], $schema->parse(['name' => 'John']));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testMissingElementIsCaughtInsideATuple(): void
    {
        $schema = S::tuple(S::string(), S::number()->catch(0));

        $this->assertSame(['a', 0], $schema->parse(['a']));
    }

    public function testOtherPropertiesStillFail(): void
    {
        $schema = S::object([
            'name' => S::string(),
            'age' => S::integer()->catch(0),
        ]);

        $this->assertFalse($schema->safeParse(['name' => 123, 'age' => 'abc'])->isValid());
    }

    public function testCatchReturnsANewSchema(): void
    {
        $schema = S::string();
        $caught = $schema->catch('fallback');

        $this->assertNotSame($schema, $caught);
        $this->assertFalse($schema->hasCatchValue());
        $this->assertTrue($caught->hasCatchValue());
        $this->assertFalse($schema->safeParse(123)->isValid());
    }
}
