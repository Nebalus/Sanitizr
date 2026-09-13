<?php

declare(strict_types=1);

namespace UnitTesting\Trait\Fixture;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

final class FlatEmail
{
    use SanitizrValueObjectTrait;

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->email();
    }
}
