<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use InvalidArgumentException;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;

class SanitizrStringTest extends TestCase
{
    /**
     * @throws SanitizrValidationException
     */
    public function testPhoneValidationSuccess(): void
    {
        $schema = new SanitizrString();
        $schema = $schema->phone();

        $this->assertSame('+1234567890', $schema->parse('+1234567890'));
        $this->assertSame('123-456-7890', $schema->parse('123-456-7890'));
        $this->assertSame('+44 (0) 20 1234 5678', $schema->parse('+44 (0) 20 1234 5678'));
    }

    public function testPhoneValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = new SanitizrString();
        $schema = $schema->phone();
        $schema->parse('not_a_phone_number');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testTransform(): void
    {
        $schema = (new SanitizrString())->email()->transform(function (string $input) {
            return (object) ['email' => $input];
        });

        $result = $schema->parse('test@example.com');
        $this->assertIsObject($result);
        $this->assertSame('test@example.com', $result->email);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testAlphanumericValidationSuccess(): void
    {
        $schema = (new SanitizrString())->alphanumeric();

        $this->assertSame('A1B2C3', $schema->parse('A1B2C3'));
        $this->assertSame('justLetters', $schema->parse('justLetters'));
        $this->assertSame('123456', $schema->parse('123456'));
    }

    public function testAlphanumericValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->alphanumeric();
        $schema->parse('not_alphanumeric_123!');
    }
    public function testUuidValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->uuid();
        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', $schema->parse('123e4567-e89b-12d3-a456-426614174000'));
    }

    public function testUuidValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->uuid();
        $schema->parse('not-a-uuid');
    }

    public function testHttpUrlValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->httpUrl();
        $this->assertSame('http://example.com', $schema->parse('http://example.com'));
        $this->assertSame('https://example.com', $schema->parse('https://example.com'));
    }

    public function testHttpUrlValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->httpUrl();
        $schema->parse('ftp://example.com');
    }

    public function testHostnameValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->hostname();
        $this->assertSame('example.com', $schema->parse('example.com'));
    }

    public function testHostnameValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->hostname();
        $schema->parse('not a valid hostname');
    }

    public function testEmojiValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->emoji();
        $this->assertSame('😀', $schema->parse('😀'));
    }

    public function testEmojiValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->emoji();
        $schema->parse('a');
    }

    public function testBase64ValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->base64();
        $this->assertSame('SGVsbG8gV29ybGQ=', $schema->parse('SGVsbG8gV29ybGQ='));
    }

    public function testBase64ValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->base64();
        $schema->parse('Not base64!');
    }

    public function testBase64urlValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->base64url();
        $this->assertSame('SGVsbG8td29ybGQ', $schema->parse('SGVsbG8td29ybGQ'));
    }

    public function testBase64urlValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->base64url();
        $schema->parse('SGVsbG8gV29ybGQ=');
    }

    public function testHexValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->hex();
        $this->assertSame('1a2b3c', $schema->parse('1a2b3c'));
    }

    public function testHexValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->hex();
        $schema->parse('not hex');
    }

    public function testJwtValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->jwt();
        $this->assertSame('header.payload.signature', $schema->parse('header.payload.signature'));
    }

    public function testJwtValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->jwt();
        $schema->parse('header.payload');
    }

    public function testNanoidValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->nanoid();
        $this->assertSame('V1StGXR8_Z5jdHi6B-myT', $schema->parse('V1StGXR8_Z5jdHi6B-myT'));
    }

    public function testNanoidValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->nanoid();
        $schema->parse('too-short');
    }

    public function testCuidValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->cuid();
        $this->assertSame('cjld2cjxh0000qzrmn831i7rn', $schema->parse('cjld2cjxh0000qzrmn831i7rn'));
    }

    public function testCuidValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->cuid();
        $schema->parse('not a cuid');
    }

    public function testCuid2ValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->cuid2();
        $this->assertSame('tz4a98xxat96iws9zmbrgj3a', $schema->parse('tz4a98xxat96iws9zmbrgj3a'));
    }

    public function testCuid2ValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->cuid2();
        $schema->parse('1tz4a98xxat96iws9zmbrgj3a'); // starts with number
    }

    public function testUlidValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->ulid();
        $this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', $schema->parse('01ARZ3NDEKTSV4RRFFQ69G5FAV'));
    }

    public function testUlidValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->ulid();
        $schema->parse('not a ulid');
    }

    public function testIpv4ValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->ipv4();
        $this->assertSame('192.168.1.1', $schema->parse('192.168.1.1'));
    }

    public function testIpv4ValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->ipv4();
        $schema->parse('256.256.256.256');
    }

    public function testIpv6ValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->ipv6();
        $this->assertSame('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $schema->parse('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
    }

    public function testIpv6ValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->ipv6();
        $schema->parse('not an ipv6');
    }

    public function testMacValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->mac();
        $this->assertSame('00:1A:2B:3C:4D:5E', $schema->parse('00:1A:2B:3C:4D:5E'));
    }

    public function testMacValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->mac();
        $schema->parse('00:1A:2B:3C:4D:5Z');
    }

    public function testCidrv4ValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->cidrv4();
        $this->assertSame('192.168.1.0/24', $schema->parse('192.168.1.0/24'));
    }

    public function testCidrv4ValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->cidrv4();
        $schema->parse('192.168.1.0/33');
    }

    public function testCidrv6ValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->cidrv6();
        $this->assertSame('2001:db8::/32', $schema->parse('2001:db8::/32'));
    }

    public function testCidrv6ValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->cidrv6();
        $schema->parse('2001:db8::/129');
    }

    public function testHashValidationSuccess(): void
    {
        $schema = clone new SanitizrString();
        $schema = $schema->hash('md5');
        $this->assertSame('d41d8cd98f00b204e9800998ecf8427e', $schema->parse('d41d8cd98f00b204e9800998ecf8427e'));
    }

    public function testHashValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = clone new SanitizrString();
        $schema = $schema->hash('md5');
        $schema->parse('too-short');
    }

    public function testHashTypeIsCaseInsensitive(): void
    {
        $digest = hash('sha256', 'sanitizr');
        $schema = (new SanitizrString())->hash('SHA256');
        $this->assertSame($digest, $schema->parse($digest));
    }

    public function testHashRejectsUnknownTypeAtBuildTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new SanitizrString())->hash('sha999');
    }

    public static function hexDigestProvider(): array
    {
        return [
            'md5' => ['md5'],
            'sha1' => ['sha1'],
            'sha224' => ['sha224'],
            'sha256' => ['sha256'],
            'sha384' => ['sha384'],
            'sha512' => ['sha512'],
        ];
    }

    /**
     * @throws SanitizrValidationException
     */
    #[DataProvider('hexDigestProvider')]
    public function testDedicatedHexDigestAcceptsBothCases(string $algorithm): void
    {
        $digest = hash($algorithm, 'sanitizr');
        $schema = (new SanitizrString())->$algorithm();
        $this->assertSame($digest, $schema->parse($digest));
        $this->assertSame(strtoupper($digest), $schema->parse(strtoupper($digest)));
    }

    #[DataProvider('hexDigestProvider')]
    public function testDedicatedHexDigestRejectsWrongLength(string $algorithm): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->$algorithm();
        $schema->parse(substr(hash($algorithm, 'sanitizr'), 0, -1));
    }

    #[DataProvider('hexDigestProvider')]
    public function testDedicatedHexDigestRejectsNonHex(string $algorithm): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->$algorithm();
        $schema->parse(str_repeat('z', strlen(hash($algorithm, 'sanitizr'))));
    }

    #[DataProvider('hexDigestProvider')]
    public function testDedicatedHexDigestReportsAlgorithmAndLength(string $algorithm): void
    {
        $issue = $this->issueFrom((new SanitizrString())->$algorithm(), 'abc');

        $this->assertSame("$algorithm hash", $issue->expected);
        $this->assertSame('length:3', $issue->received);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testBcryptValidationSuccess(): void
    {
        $hash = password_hash('correct horse battery staple', PASSWORD_BCRYPT);
        $schema = (new SanitizrString())->bcrypt();
        $this->assertSame($hash, $schema->parse($hash));
    }

    public function testBcryptValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->bcrypt();
        $schema->parse('$2y$10$tooshort');
    }

    public function testBcryptRejectsIdentifierWithoutVariantLetter(): void
    {
        $valid = password_hash('correct horse battery staple', PASSWORD_BCRYPT);

        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->bcrypt();
        $schema->parse('$2$' . substr($valid, 4));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testArgon2ValidationSuccess(): void
    {
        if (! defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('Argon2 support is not compiled into this PHP build');
        }

        $argon2i = password_hash('correct horse battery staple', PASSWORD_ARGON2I);
        $argon2id = password_hash('correct horse battery staple', PASSWORD_ARGON2ID);

        $this->assertSame($argon2i, (new SanitizrString())->argon2i()->parse($argon2i));
        $this->assertSame($argon2id, (new SanitizrString())->argon2id()->parse($argon2id));
    }

    public function testArgon2iRejectsArgon2idHash(): void
    {
        if (! defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('Argon2 support is not compiled into this PHP build');
        }

        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->argon2i();
        $schema->parse(password_hash('correct horse battery staple', PASSWORD_ARGON2ID));
    }

    public function testArgon2idValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        $schema = (new SanitizrString())->argon2id();
        $schema->parse('$argon2id$v=19$m=65536,t=4,p=1$missingthehashpart');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testLengthValidationSuccess(): void
    {
        $schema = (new SanitizrString())->length(5);
        $this->assertSame('hello', $schema->parse('hello'));
    }

    public function testLengthRejectsShorterInput(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->length(5)->parse('hell');
    }

    public function testLengthRejectsLongerInput(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->length(5)->parse('hello!');
    }

    public function testLengthReportsTooSmallForShorterInput(): void
    {
        $issue = $this->issueFrom((new SanitizrString())->length(5), 'hell');

        $this->assertSame(SanitizrIssue::TOO_SMALL, $issue->code);
        $this->assertSame('length:5', $issue->expected);
        $this->assertSame('length:4', $issue->received);
    }

    public function testLengthReportsTooBigForLongerInput(): void
    {
        $issue = $this->issueFrom((new SanitizrString())->length(5), 'hello!');

        $this->assertSame(SanitizrIssue::TOO_BIG, $issue->code);
        $this->assertSame('length:5', $issue->expected);
        $this->assertSame('length:6', $issue->received);
    }

    public function testBetweenReportsDirectionalIssueCodes(): void
    {
        $schema = (new SanitizrString())->between(2, 4);

        $this->assertSame(SanitizrIssue::TOO_SMALL, $this->issueFrom($schema, 'a')->code);
        $this->assertSame(SanitizrIssue::TOO_BIG, $this->issueFrom($schema, 'abcde')->code);
    }

    public function testMinAndMaxReportDirectionalIssueCodes(): void
    {
        $this->assertSame(
            SanitizrIssue::TOO_SMALL,
            $this->issueFrom((new SanitizrString())->min(3), 'ab')->code
        );
        $this->assertSame(
            SanitizrIssue::TOO_BIG,
            $this->issueFrom((new SanitizrString())->max(3), 'abcd')->code
        );
    }

    /**
     * Parses input that is expected to fail and returns the first reported issue.
     */
    private function issueFrom(SanitizrString $schema, string $input): SanitizrIssue
    {
        try {
            $schema->parse($input);
        } catch (SanitizrValidationException $e) {
            return $e->getError()->getIssues()[0];
        }

        $this->fail('Expected a validation exception');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testMinValidation(): void
    {
        $schema = (new SanitizrString())->min(3);
        $this->assertSame('abc', $schema->parse('abc'));
        $this->assertSame('abcd', $schema->parse('abcd'));
    }

    public function testMinValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->min(3)->parse('ab');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testMaxValidation(): void
    {
        $schema = (new SanitizrString())->max(3);
        $this->assertSame('abc', $schema->parse('abc'));
        $this->assertSame('a', $schema->parse('a'));
    }

    public function testMaxValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->max(3)->parse('abcd');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testBetweenValidationIsInclusive(): void
    {
        $schema = (new SanitizrString())->between(2, 4);
        $this->assertSame('ab', $schema->parse('ab'));
        $this->assertSame('abcd', $schema->parse('abcd'));
    }

    public function testBetweenRejectsBelowMinimum(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->between(2, 4)->parse('a');
    }

    public function testBetweenRejectsAboveMaximum(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->between(2, 4)->parse('abcde');
    }

    public function testLengthIssueCarriesExpectedAndReceived(): void
    {
        $issue = $this->issueFrom((new SanitizrString())->min(4), 'ab');

        $this->assertSame('min:4', $issue->expected);
        $this->assertSame('length:2', $issue->received);
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testUppercaseValidation(): void
    {
        $schema = (new SanitizrString())->uppercase();
        $this->assertSame('ABC', $schema->parse('ABC'));
        $this->assertSame('A1!', $schema->parse('A1!'));
    }

    public function testUppercaseValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->uppercase()->parse('AbC');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testLowercaseValidation(): void
    {
        $schema = (new SanitizrString())->lowercase();
        $this->assertSame('abc', $schema->parse('abc'));
    }

    public function testLowercaseValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->lowercase()->parse('aBc');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testIncludesValidation(): void
    {
        $schema = (new SanitizrString())->includes('bar');
        $this->assertSame('foobarbaz', $schema->parse('foobarbaz'));
    }

    public function testIncludesValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->includes('bar')->parse('foobaz');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testRegexValidation(): void
    {
        $schema = (new SanitizrString())->regex('/^[a-z]+-\d+$/');
        $this->assertSame('item-42', $schema->parse('item-42'));
    }

    public function testRegexValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->regex('/^[a-z]+-\d+$/')->parse('Item-42');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testEmailValidation(): void
    {
        $schema = (new SanitizrString())->email();
        $this->assertSame('test@example.com', $schema->parse('test@example.com'));
        $this->assertSame('a.b+c@sub.example.co.uk', $schema->parse('a.b+c@sub.example.co.uk'));
    }

    public function testEmailValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->email()->parse('not-an-email');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testUrlValidation(): void
    {
        $schema = (new SanitizrString())->url();
        $this->assertSame('https://example.com/a?b=1', $schema->parse('https://example.com/a?b=1'));
    }

    public function testUrlValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->url()->parse('not a url');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testStartsWithValidation(): void
    {
        $schema = (new SanitizrString())->startsWith('pre_');
        $this->assertSame('pre_value', $schema->parse('pre_value'));
    }

    public function testStartsWithValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->startsWith('pre_')->parse('value_pre_');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testEndsWithValidation(): void
    {
        $schema = (new SanitizrString())->endsWith('.txt');
        $this->assertSame('notes.txt', $schema->parse('notes.txt'));
    }

    public function testEndsWithValidationFailure(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->endsWith('.txt')->parse('notes.txt.bak');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testDigitsValidation(): void
    {
        $schema = (new SanitizrString())->digits();
        $this->assertSame('0123456789', $schema->parse('0123456789'));
    }

    public function testDigitsRejectsNonDigits(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->digits()->parse('12a');
    }

    public function testDigitsRejectsEmptyString(): void
    {
        $this->expectException(SanitizrValidationException::class);
        (new SanitizrString())->digits()->parse('');
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testTrimTransformation(): void
    {
        $schema = (new SanitizrString())->trim();
        $this->assertSame('hello', $schema->parse("  hello \n"));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testCaseTransformations(): void
    {
        $this->assertSame('abc', (new SanitizrString())->toLowerCase()->parse('AbC'));
        $this->assertSame('ABC', (new SanitizrString())->toUpperCase()->parse('AbC'));
        $this->assertSame('Hello World', (new SanitizrString())->toTitleCase()->parse('hELLO wORLD'));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testStripTagsTransformation(): void
    {
        $schema = (new SanitizrString())->stripTags();
        $this->assertSame('bold text', $schema->parse('<b>bold</b> text'));

        $keepBold = (new SanitizrString())->stripTags('<b>');
        $this->assertSame('<b>bold</b> text', $keepBold->parse('<b>bold</b> <i>text</i>'));
    }

    /**
     * @throws SanitizrValidationException
     */
    public function testHtmlSpecialCharsTransformation(): void
    {
        $schema = (new SanitizrString())->htmlSpecialChars();
        $this->assertSame('&lt;a href=&quot;x&quot;&gt;', $schema->parse('<a href="x">'));
    }

    /**
     * Transforms are applied before checks regardless of the order they were chained in.
     *
     * @throws SanitizrValidationException
     */
    public function testTransformsRunBeforeChecksRegardlessOfChainOrder(): void
    {
        $trimThenCheck = (new SanitizrString())->trim()->length(5);
        $checkThenTrim = (new SanitizrString())->length(5)->trim();

        $this->assertSame('hello', $trimThenCheck->parse('  hello  '));
        $this->assertSame('hello', $checkThenTrim->parse('  hello  '));
    }

    /**
     * Every rule returns a clone, so a base schema is never mutated by deriving from it.
     *
     * @throws SanitizrValidationException
     */
    public function testRulesReturnCloneAndLeaveBaseSchemaUntouched(): void
    {
        $base = new SanitizrString();
        $restricted = $base->min(5);

        $this->assertNotSame($base, $restricted);
        $this->assertSame('ab', $base->parse('ab'));

        $this->expectException(SanitizrValidationException::class);
        $restricted->parse('ab');
    }

    public function testCustomMessageOverridesDefault(): void
    {
        $issue = $this->issueFrom((new SanitizrString())->sha256('my custom message'), 'nope');

        $this->assertSame('my custom message', $issue->message);
    }
}
