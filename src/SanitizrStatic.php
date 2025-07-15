<?php

namespace Nebalus\Sanitizr;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\SanitizrBatchSchema;
use Nebalus\Sanitizr\Schema\SanitizrArraySchema;
use Nebalus\Sanitizr\Schema\SanitizrBooleanSchema;
use Nebalus\Sanitizr\Schema\SanitizrLiterallySchema;
use Nebalus\Sanitizr\Schema\SanitizrNullSchema;
use Nebalus\Sanitizr\Schema\SanitizrNumberSchema;
use Nebalus\Sanitizr\Schema\SanitizrObjectSchema;
use Nebalus\Sanitizr\Schema\SanitizrStringSchema;

class SanitizrStatic
{
    public static function literally(mixed $literallyValue): SanitizrLiterallySchema
    {
        return new SanitizrLiterallySchema($literallyValue);
    }

    public static function boolean(): SanitizrBooleanSchema
    {
        return new SanitizrBooleanSchema();
    }

    public static function number(): SanitizrNumberSchema
    {
        return new SanitizrNumberSchema();
    }

    public static function string(): SanitizrStringSchema
    {
        return new SanitizrStringSchema();
    }

    public static function array(AbstractSanitizrSchema $schema): SanitizrArraySchema
    {
        return new SanitizrArraySchema($schema);
    }

    public static function object(array $schemas): SanitizrObjectSchema
    {
        return new SanitizrObjectSchema($schemas);
    }

    public static function batch(AbstractSanitizrSchema ...$schemas): SanitizrBatchSchema
    {
        return new SanitizrBatchSchema(...$schemas);
    }

    public static function null(): SanitizrNullSchema
    {
        return new SanitizrNullSchema();
    }
}
