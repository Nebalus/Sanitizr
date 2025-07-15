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

class Sanitizr
{
    public function literal(mixed $value): SanitizrLiteral
    {
        return new SanitizrLiteral($value);
    }

    public function boolean(): SanitizrBoolean
    {
        return new SanitizrBoolean();
    }

    public function number(): SanitizrNumber
    {
        return new SanitizrNumber();
    }

    public function string(): SanitizrString
    {
        return new SanitizrString();
    }

    public function array(AbstractSanitizrSchema $schema): SanitizrArray
    {
        return new SanitizrArray($schema);
    }

    public function object(array $schemas): SanitizrObject
    {
        return new SanitizrObject($schemas);
    }

    public function batch(AbstractSanitizrSchema ...$schemas): SanitizrBatch
    {
        return new SanitizrBatch(...$schemas);
    }

    public function null(): SanitizrNull
    {
        return new SanitizrNull();
    }

    public static function nullable(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        return $schema->nullable();
    }
}
