<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\SanitizrPipe;
use PHPUnit\Framework\TestCase;

class SanitizrPipeTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testOutputOfTheFirstSchemaIsParsedByTheSecond(): void
    {
        $schema = S::string()->digits()->transform(fn(string $value) => (int) $value)->pipe(S::number()->gt(0));

        $this->assertInstanceOf(SanitizrPipe::class, $schema);
        $this->assertSame(42, $schema->parse('42'));
    }

    public function testFailureInTheFirstSchema(): void
    {
        $schema = S::string()->digits()->transform(fn(string $value) => (int) $value)->pipe(S::number()->gt(0));

        $error = $schema->safeParse('abc')->getError();

        $this->assertSame(SanitizrIssue::INVALID_STRING, $error->getIssues()[0]->code);
    }

    public function testFailureInTheSecondSchema(): void
    {
        $schema = S::string()->digits()->transform(fn(string $value) => (int) $value)->pipe(S::number()->gt(0));

        $error = $schema->safeParse('0')->getError();

        $this->assertSame(SanitizrIssue::TOO_SMALL, $error->getIssues()[0]->code);
    }

    public function testFailureInsideAnObjectKeepsThePath(): void
    {
        $schema = S::object([
            'age' => S::pipe(S::string()->transform(fn(string $value) => (int) $value), S::number()->gte(18)),
        ]);

        $error = $schema->safeParse(['age' => '12'])->getError();

        $this->assertSame('age', $error->getIssues()[0]->getPathString());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testStaticAndInstanceFactories(): void
    {
        $in = S::string()->trim();
        $out = S::string()->min(3);

        $this->assertSame('abc', S::pipe($in, $out)->parse('  abc  '));
        $this->assertSame('abc', (new Sanitizr())->pipe($in, $out)->parse('  abc  '));
        $this->assertSame($in, S::pipe($in, $out)->getInSchema());
        $this->assertSame($out, S::pipe($in, $out)->getOutSchema());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testPipeModifiersApplyToThePipeItself(): void
    {
        $schema = S::pipe(S::string(), S::string()->min(3));

        $this->assertNull($schema->nullable()->parse(null));
        $this->assertSame('def', $schema->default('def')->parse(null));
        $this->assertSame([], S::object(['name' => $schema->optional()])->parse([]));
        $this->assertSame('ABC', $schema->transform(fn(string $value) => strtoupper($value))->parse('abc'));
    }
}
