<?php

namespace Nebalus\Sanitizr;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrBoolean;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrNull;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrNumber;
use Nebalus\Sanitizr\Schema\Primitive\SanitizrString;
use Nebalus\Sanitizr\Schema\SanitizrArray;
use Nebalus\Sanitizr\Schema\SanitizrBatch;
use Nebalus\Sanitizr\Schema\SanitizrLiteral;
use Nebalus\Sanitizr\Schema\SanitizrObject;

class SanitizrStatic
{
    public static function literal(mixed $value): SanitizrLiteral
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

    public static function nullable(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullable();
    }

    public static function nullish(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullish();
    }

    public static function optional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->optional();
    }

    public static function nonOptional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nonOptional();
    }

    public static function float(): SanitizrNumber
    {
        $numberSchema = new SanitizrNumber();
        return $numberSchema->float();
    }

    public static function integer(): SanitizrNumber
    {
        $numberSchema = new SanitizrNumber();
        return $numberSchema->integer();
    }
}
