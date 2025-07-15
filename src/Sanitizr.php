<?php

namespace Nebalus\Sanitizr;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\Primitives\SanitizrBooleanSchema;
use Nebalus\Sanitizr\Schema\Primitives\SanitizrNumberSchema;
use Nebalus\Sanitizr\Schema\Primitives\SanitizrStringSchema;
use Nebalus\Sanitizr\Schema\SanitizrArraySchema;
use Nebalus\Sanitizr\Schema\SanitizrBatchSchema;
use Nebalus\Sanitizr\Schema\SanitizrLiteralSchema;
use Nebalus\Sanitizr\Schema\SanitizrNullSchema;
use Nebalus\Sanitizr\Schema\SanitizrObjectSchema;

class Sanitizr
{
    public function literal(mixed $literalValue): SanitizrLiteralSchema
    {
        return new SanitizrLiteralSchema($literalValue);
    }

    public function boolean(): SanitizrBooleanSchema
    {
        return new SanitizrBooleanSchema();
    }

    public function number(): SanitizrNumberSchema
    {
        return new SanitizrNumberSchema();
    }

    public function string(): SanitizrStringSchema
    {
        return new SanitizrStringSchema();
    }

    public function array(AbstractSanitizrSchema $schema): SanitizrArraySchema
    {
        return new SanitizrArraySchema($schema);
    }

    public function object(array $schemas): SanitizrObjectSchema
    {
        return new SanitizrObjectSchema($schemas);
    }

    public function batch(AbstractSanitizrSchema ...$schemas): SanitizrBatchSchema
    {
        return new SanitizrBatchSchema(...$schemas);
    }

    public function null(): SanitizrNullSchema
    {
        return new SanitizrNullSchema();
    }
}
