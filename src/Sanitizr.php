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

    public function nullable(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullable();
    }

    public function nullish(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullish();
    }

    public function optional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->optional();
    }

    public function nonOptional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nonOptional();
    }

    public function float(): SanitizrNumber
    {
        $numberSchema = new SanitizrNumber();
        return $numberSchema->float();
    }

    public function integer(): SanitizrNumber
    {
        $numberSchema = new SanitizrNumber();
        return $numberSchema->integer();
    }
}
