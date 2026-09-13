<?php

declare(strict_types=1);

namespace UnitTesting\Trait\Fixture;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

final class LowerCaseValue extends CasedValue
{
    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->regex('/^[a-z]+$/');
    }
}
