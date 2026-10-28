<?php

declare(strict_types=1);

namespace UnitTesting\Trait\Fixture;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

/**
 * A value object whose schema is an object schema that transforms into an instance.
 */
final class Address
{
    use SanitizrValueObjectTrait;

    private function __construct(
        public readonly string $city,
        public readonly string $zip,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::object([
            'city' => S::string(),
            'zip' => S::string()->digits(),
        ])->transform(fn(array $data) => new self($data['city'], $data['zip']));
    }
}
