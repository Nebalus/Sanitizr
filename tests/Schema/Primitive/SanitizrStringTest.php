<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
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
}
