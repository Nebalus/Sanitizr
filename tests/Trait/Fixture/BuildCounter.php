<?php

declare(strict_types=1);

namespace UnitTesting\Trait\Fixture;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

final class BuildCounter
{
    use SanitizrValueObjectTrait;

    public static int $builds = 0;

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        self::$builds++;

        return S::string();
    }
}
