<?php

namespace Nebalus\Sanitizr;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\SanitizrArraySchema;
use Nebalus\Sanitizr\Schema\SanitizrBooleanSchema;
use Nebalus\Sanitizr\Schema\SanitizrDateSchema;
use Nebalus\Sanitizr\Schema\SanitizrFileSchema;
use Nebalus\Sanitizr\Schema\SanitizrNullSchema;
use Nebalus\Sanitizr\Schema\SanitizrNumberSchema;
use Nebalus\Sanitizr\Schema\SanitizrObjectSchema;
use Nebalus\Sanitizr\Schema\SanitizrStringSchema;

class Sanitizr
{
    public static function date(): SanitizrDateSchema
    {
        return new SanitizrDateSchema();
    }

    public static function file(): SanitizrFileSchema
    {
        return new SanitizrFileSchema();
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

    public static function object(array $schema): SanitizrObjectSchema
    {
        return new SanitizrObjectSchema($schema);
    }

    public static function null(): SanitizrNullSchema
    {
        return new SanitizrNullSchema();
    }
}
