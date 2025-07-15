<?php

namespace Nebalus\Sanitizr;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\SanitizrArraySchema;
use Nebalus\Sanitizr\Schema\SanitizrBatchSchema;
use Nebalus\Sanitizr\Schema\SanitizrBooleanSchema;
use Nebalus\Sanitizr\Schema\SanitizrLiterallySchema;
use Nebalus\Sanitizr\Schema\SanitizrNullSchema;
use Nebalus\Sanitizr\Schema\SanitizrNumberSchema;
use Nebalus\Sanitizr\Schema\SanitizrObjectSchema;
use Nebalus\Sanitizr\Schema\SanitizrStringSchema;

class Sanitizr
{
    public function literally(mixed $literallyValue): SanitizrLiterallySchema
    {
        return new SanitizrLiterallySchema($literallyValue);
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
