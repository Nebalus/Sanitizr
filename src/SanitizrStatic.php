<?php

namespace Nebalus\Sanitizr;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\Primitives\SanitizrBoolean;
use Nebalus\Sanitizr\Schema\Primitives\SanitizrNumber;
use Nebalus\Sanitizr\Schema\Primitives\SanitizrString;
use Nebalus\Sanitizr\Schema\SanitizrArray;
use Nebalus\Sanitizr\Schema\SanitizrBatch;
use Nebalus\Sanitizr\Schema\SanitizrLiteral;
use Nebalus\Sanitizr\Schema\SanitizrNull;
use Nebalus\Sanitizr\Schema\SanitizrObject;

class SanitizrStatic
{
    public static function literally(mixed $value): SanitizrLiteral
    {
        return new SanitizrLiteral($value);
    }

    public static function boolean(): SanitizrBoolean
    {
        return new SanitizrBoolean();
    }

    public static function number(): SanitizrNumber
    {
        return new SanitizrNumber();
    }

    public static function string(): SanitizrString
    {
        return new SanitizrString();
    }

    public static function array(AbstractSanitizrSchema $schema): SanitizrArray
    {
        return new SanitizrArray($schema);
    }

    public static function object(array $schemas): SanitizrObject
    {
        return new SanitizrObject($schemas);
    }

    public static function batch(AbstractSanitizrSchema ...$schemas): SanitizrBatch
    {
        return new SanitizrBatch(...$schemas);
    }

    public static function null(): SanitizrNull
    {
        return new SanitizrNull();
    }
}
