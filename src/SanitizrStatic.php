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
    /**
     * Creates a schema that matches a specific literal value.
     *
     * @param mixed $value The exact value to be matched by the schema.
     * @return SanitizrLiteral A schema instance representing the specified literal value.
     */
    public static function literal(mixed $value): SanitizrLiteral
    {
        return new SanitizrLiteral($value);
    }

    /**
     * Creates a schema for validating boolean values.
     *
     * @return SanitizrBoolean A schema instance representing a boolean type.
     */
    public static function boolean(): SanitizrBoolean
    {
        return new SanitizrBoolean();
    }

    /**
     * Creates a schema for validating numeric values.
     *
     * @return SanitizrNumber A schema instance representing a number.
     */
    public static function number(): SanitizrNumber
    {
        return new SanitizrNumber();
    }

    /**
     * Creates a schema for validating string values.
     *
     * @return SanitizrString A new string schema instance.
     */
    public static function string(): SanitizrString
    {
        return new SanitizrString();
    }

    /**
     * Creates a schema for an array whose elements must conform to the specified schema.
     *
     * @param AbstractSanitizrSchema $schema The schema that each array element must satisfy.
     * @return SanitizrArray The array schema instance.
     */
    public static function array(AbstractSanitizrSchema $schema): SanitizrArray
    {
        return new SanitizrArray($schema);
    }

    /**
     * Creates a schema for an object with specified property schemas.
     *
     * @param array $schemas An associative array mapping property names to their corresponding schemas.
     * @return SanitizrObject The object schema instance.
     */
    public static function object(array $schemas): SanitizrObject
    {
        return new SanitizrObject($schemas);
    }

    /**
     * Creates a batch schema that aggregates multiple schemas.
     *
     * @param AbstractSanitizrSchema ...$schemas One or more schemas to include in the batch.
     * @return SanitizrBatch The batch schema instance containing the provided schemas.
     */
    public static function batch(AbstractSanitizrSchema ...$schemas): SanitizrBatch
    {
        return new SanitizrBatch(...$schemas);
    }

    /**
     * Creates a schema that matches only null values.
     *
     * @return SanitizrNull A schema instance representing a null value.
     */
    public static function null(): SanitizrNull
    {
        return new SanitizrNull();
    }

    /**
     * Returns a clone of the given schema that allows null values.
     *
     * @param AbstractSanitizrSchema $schema The schema to make nullable.
     * @return AbstractSanitizrSchema A cloned schema instance configured to accept null.
     */
    public static function nullable(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullable();
    }

    /**
     * Returns a clone of the given schema that allows both null and undefined values.
     *
     * @param AbstractSanitizrSchema $schema The schema to modify.
     * @return AbstractSanitizrSchema The modified schema accepting null or undefined.
     */
    public static function nullish(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nullish();
    }

    /**
     * Returns a clone of the given schema marked as optional.
     *
     * The resulting schema will not require a value to be present during validation.
     *
     * @param AbstractSanitizrSchema $schema The schema to make optional.
     * @return AbstractSanitizrSchema The cloned schema marked as optional.
     */
    public static function optional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->optional();
    }

    /**
     * Returns a clone of the given schema marked as non-optional.
     *
     * @param AbstractSanitizrSchema $schema The schema to modify.
     * @return AbstractSanitizrSchema The cloned schema with the optional flag removed.
     */
    public static function nonOptional(AbstractSanitizrSchema $schema): AbstractSanitizrSchema
    {
        $clonedSchema = clone $schema;
        return $clonedSchema->nonOptional();
    }
}
