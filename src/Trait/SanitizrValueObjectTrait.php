<?php

namespace Nebalus\Sanitizr\Trait;

use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

trait SanitizrValueObjectTrait
{
    /**
     * Compiled schemas, one entry per concrete class.
     *
     * A static property declared in a trait is copied into the class that uses
     * the trait exactly once, and every subclass of that class shares it. So a
     * single cached schema would mean that, in a hierarchy such as
     * `abstract class Id { use SanitizrValueObjectTrait; }` with `UserId` and
     * `OrderId` extending it, whichever subclass was touched first would decide
     * what all the others validate. Keying the cache by `static::class` gives
     * each concrete class its own slot in the one shared property.
     *
     * @var array<class-string, AbstractSanitizrSchema>
     */
    private static array $schemaCache = [];

    /**
     * Returns a clone of this class's compiled schema, building it on first use.
     *
     * Late-bound (`static::`) rather than `self::` so that the schema is defined
     * by the class the call was made on, not the class that uses the trait —
     * calling `self::defineSchema()` from a using base class would try to invoke
     * the base class's abstract method and fail.
     */
    public static function getSchema(): AbstractSanitizrSchema
    {
        return clone (self::$schemaCache[static::class] ??= static::defineSchema());
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
