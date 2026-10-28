<?php

declare(strict_types=1);

namespace UnitTesting\Trait\Fixture;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

/**
 * An abstract base with concrete ids below it, the shape the per-class schema
 * cache and the late-bound constructor both exist for.
 */
abstract class Id
{
    use SanitizrValueObjectTrait;

    final private function __construct(
        private readonly string $value,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->trim()->toLowerCase()->uuid();
    }

    public function asString(): string
    {
        return $this->value;
    }
}
