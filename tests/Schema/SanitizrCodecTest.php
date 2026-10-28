<?php

declare(strict_types=1);

namespace UnitTesting\Schema;

use DateTimeImmutable;
use LogicException;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\SanitizrCodec;
use PHPUnit\Framework\TestCase;
use UnitTesting\Schema\Fixture\DateTimeSchema;

class SanitizrCodecTest extends TestCase
{
    private static function stringToInt(): SanitizrCodec
    {
        return S::codec(
            S::string()->digits(),
            S::integer(),
            decode: fn(string $value) => (int) $value,
            encode: fn(int $value) => (string) $value,
        );
    }

    private static function isoDatetime(): SanitizrCodec
    {
        return S::codec(
            S::string(),
            new DateTimeSchema(),
            decode: fn(string $value) => new DateTimeImmutable($value),
            encode: fn(DateTimeImmutable $value) => $value->format(DATE_ATOM),
        );
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testDecodeAndEncode(): void
    {
        $codec = self::stringToInt();

        $this->assertSame(42, $codec->parse('42'));
        $this->assertSame(42, $codec->decode('42'));
        $this->assertSame('42', $codec->encode(42));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testDateRoundTrip(): void
    {
        $codec = self::isoDatetime();

        $date = $codec->parse('2026-10-28T12:30:00+00:00');

        $this->assertInstanceOf(DateTimeImmutable::class, $date);
        $this->assertSame('2026-10-28T12:30:00+00:00', $codec->encode($date));
    }

    public function testDecodeValidatesBothSides(): void
    {
        $codec = self::stringToInt();

        $this->assertSame(SanitizrIssue::INVALID_STRING, $codec->safeParse('abc')->getError()->getIssues()[0]->code);
        $this->assertSame(SanitizrIssue::INVALID_TYPE, $codec->safeParse(42)->getError()->getIssues()[0]->code);
    }

    public function testSafeEncodeReportsAnInvalidValue(): void
    {
        $result = self::stringToInt()->safeEncode('abc');

        $this->assertFalse($result->isValid());
        $this->assertSame(SanitizrIssue::INVALID_TYPE, $result->getError()->getIssues()[0]->code);
        $this->assertSame('42', self::stringToInt()->safeEncode(42)->getValue());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testEncodedValueIsValidatedByTheInputSchema(): void
    {
        $codec = S::codec(
            S::string()->digits(),
            S::integer(),
            decode: fn(string $value) => (int) $value,
            encode: fn(int $value) => (string) $value,
        );

        $this->assertFalse($codec->safeEncode(-5)->isValid());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testJsonCodecEncodesAnObjectOutput(): void
    {
        $codec = S::codec(
            S::string(),
            S::object(['name' => S::string()]),
            decode: fn(string $value) => json_decode($value, true),
            encode: fn(array $value) => json_encode($value),
        );

        $this->assertSame(['name' => 'John'], $codec->parse('{"name":"John"}'));
        $this->assertSame('{"name":"John"}', $codec->encode(['name' => 'John']));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testWholeTreeIsEncoded(): void
    {
        $schema = S::object([
            'createdAt' => self::isoDatetime(),
            'scores' => S::array(self::stringToInt()),
            'pair' => S::tuple(self::stringToInt(), S::string()),
            'note' => S::string()->optional(),
        ]);
        $input = [
            'createdAt' => '2026-10-28T12:30:00+00:00',
            'scores' => ['1', '2'],
            'pair' => ['3', 'x'],
        ];

        $decoded = $schema->parse($input);

        $this->assertInstanceOf(DateTimeImmutable::class, $decoded['createdAt']);
        $this->assertSame([1, 2], $decoded['scores']);
        $this->assertSame($input, $schema->encode($decoded));
    }

    public function testEncodeErrorKeepsThePath(): void
    {
        $schema = S::object(['scores' => S::array(self::stringToInt())]);

        $error = $schema->safeEncode(['scores' => [1, 'two']])->getError();

        $this->assertSame('scores.1', $error->getIssues()[0]->getPathString());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testUnionEncodesWithTheFirstMatchingSchema(): void
    {
        $schema = S::union(self::stringToInt(), S::boolean());

        $this->assertSame('5', $schema->encode(5));
        $this->assertTrue($schema->encode(true));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testPipeEncodesInReverse(): void
    {
        $schema = S::pipe(self::stringToInt(), S::integer()->gt(0));

        $this->assertSame(7, $schema->parse('7'));
        $this->assertSame('7', $schema->encode(7));
        $this->assertFalse($schema->safeEncode(0)->isValid());
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testNullableCodecEncodesNull(): void
    {
        $this->assertNull(self::stringToInt()->nullable()->encode(null));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testSchemasWithoutCodecsEncodeAsTheyParse(): void
    {
        $this->assertSame('abc', S::string()->trim()->encode('  abc '));
        $this->assertSame(['a' => 1], S::object(['a' => S::number()])->encode(['a' => 1]));
        $this->assertFalse(S::string()->safeEncode(1)->isValid());
    }

    public function testTransformCannotBeEncoded(): void
    {
        $this->expectException(LogicException::class);

        S::string()->transform(fn(string $value) => strtoupper($value))->encode('abc');
    }

    public function testNestedTransformCannotBeEncoded(): void
    {
        $this->expectException(LogicException::class);

        S::object(['name' => S::string()->transform(fn(string $value) => strtoupper($value))])
            ->safeEncode(['name' => 'abc']);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testInstanceFactory(): void
    {
        $codec = (new Sanitizr())->codec(
            S::string(),
            S::integer(),
            decode: intval(...),
            encode: strval(...),
        );

        $this->assertSame(3, $codec->parse('3'));
        $this->assertSame('3', $codec->encode(3));
    }
}
