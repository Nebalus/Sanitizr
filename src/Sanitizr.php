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
    /**
     * Creates a schema that matches a specific literal value.
     *
     * @param mixed $value The exact value the schema should match.
     * @return SanitizrLiteral The schema instance for the specified literal value.
     */
    public function literal(mixed $value): SanitizrLiteral
    {
        return new SanitizrLiteral($value);
    }

    /**
     * Creates a schema for validating boolean values.
     *
     * @return SanitizrBoolean The boolean schema instance.
     */
    public function boolean(): SanitizrBoolean
    {
        return new SanitizrBoolean();
    }

    /**
     * Creates a schema for validating and sanitizing numeric values.
     *
     * @return SanitizrNumber The schema instance for numbers.
     */
    public function number(): SanitizrNumber
    {
        return new SanitizrNumber();
    }

    /**
     * Creates a schema for validating and sanitizing string values.
     *
     * @return SanitizrString The string schema instance.
     */
    public function string(): SanitizrString
    {
        return new SanitizrString();
    }

    /**
     * Creates an array schema that validates arrays of the specified element schema.
     *
     * @param AbstractSanitizrSchema $schema The schema to apply to each array element.
     * @return SanitizrArray The array schema instance.
     */
    public function array(AbstractSanitizrSchema $schema): SanitizrArray
    {
        return new SanitizrArray($schema);
    }

    /**
     * Creates a schema for validating and sanitizing objects with specified property schemas.
     *
     * @param array $schemas An associative array mapping property names to their corresponding schema instances.
     * @return SanitizrObject The object schema instance.
     */
    public function object(array $schemas): SanitizrObject
    {
        return new SanitizrObject($schemas);
    }

    /**
     * Creates a batch schema that applies multiple schemas in sequence.
     *
     * @param AbstractSanitizrSchema ...$schemas One or more schemas to be applied in batch.
     * @return SanitizrBatch The batch schema instance.
     */
    public function batch(AbstractSanitizrSchema ...$schemas): SanitizrBatch
    {
        return new SanitizrBatch(...$schemas);
    }

    /**
     * Creates a schema that matches only null values.
     *
     * @return SanitizrNull A schema instance for validating null.
     */
    public function null(): SanitizrNull
    {
        return new SanitizrNull();
    }

    /**
     * Returns a new schema instance that allows null values in addition to the original schema's constraints.
     *
     * @param AbstractSanitizrSchema $schema The base schema to modify.
     * @return AbstractSanitizrSchema A cloned schema configured to accept null values.
     */
    public function nullable(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullable();
    }

    /**
     * Returns a new schema instance that allows both `null` and `undefined` values in addition to the original schema's constraints.
     *
     * @param AbstractSanitizrSchema $schema The base schema to modify.
     * @return AbstractSanitizrSchema A schema accepting `null`, `undefined`, or values matching the original schema.
     */
    public function nullish(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullish();
    }

    /**
     * Returns a new schema instance that marks the given schema as optional.
     *
     * @param AbstractSanitizrSchema $schema The schema to modify.
     * @return AbstractSanitizrSchema The modified schema allowing undefined or missing values.
     */
    public function optional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->optional();
    }

    /**
     * Returns a new schema instance with the optional modifier removed.
     *
     * The returned schema will require a value to be present during validation.
     *
     * @param AbstractSanitizrSchema $schema The schema to modify.
     * @return AbstractSanitizrSchema The non-optional schema.
     */
    public function nonOptional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nonOptional();
    }
}
