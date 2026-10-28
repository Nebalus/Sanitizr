<?php

declare(strict_types=1);

namespace UnitTesting\Trait\Fixture;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

/**
 * A value object over an object schema: the parsed value is an array, so the
 * constructor cannot take it as-is and fromParsed() is overridden.
 */
final class Point
{
    use SanitizrValueObjectTrait;

    private function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::object([
            'x' => S::integer(),
            'y' => S::integer(),
        ]);
    }

    protected static function fromParsed(mixed $value): static
    {
        return new self($value['x'], $value['y']);
    }
}
