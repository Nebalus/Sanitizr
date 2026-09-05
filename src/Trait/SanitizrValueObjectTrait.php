<?php

namespace Nebalus\Sanitizr\Trait;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

trait SanitizrValueObjectTrait
{
    private static AbstractSanitizrSchema $schemaCache;

    public static function getSchema(): AbstractSanitizrSchema
    {
        if (isset(self::$schemaCache)) {
            return clone self::$schemaCache;
        }
        self::$schemaCache = self::defineSchema();
        return clone self::$schemaCache;
    }

    /**
     * Checks whether the given value satisfies the schema of this value object.
     *
     * Only the validity of the input is reported; any exception thrown while building
     * the schema itself is propagated, since that indicates a faulty schema definition
     * rather than an invalid input value.
     *
     * @param mixed $value The value to validate against the schema.
     * @return bool True if the value satisfies the schema, false otherwise.
     */
    public static function isValid(mixed $value): bool
    {
        return static::getSchema()->safeParse($value)->isValid();
    }

    abstract protected static function defineSchema(): AbstractSanitizrSchema;
}
