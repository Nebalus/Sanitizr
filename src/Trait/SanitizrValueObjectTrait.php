<?php

namespace Nebalus\Sanitizr\Trait;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Schema\SanitizrValueObject;

/**
 * Gives a value object a schema and the constructors that go with it.
 *
 * The using class defines {@see defineSchema()} — how a raw input is validated
 * and normalised — and gets:
 *
 * - {@see getSchema()}: that schema, yielding the normalised *value*.
 * - {@see getValueObjectSchema()}: the same schema wrapped so that it yields
 *   the *instance*. Put it in a larger schema (`S::object(['id' => UserId::getValueObjectSchema()])`)
 *   and the parsed result carries a `UserId` where the input carried a string.
 * - {@see from()} / {@see tryFrom()}: parse one input straight into an instance.
 * - {@see isValid()}: does an input satisfy the schema at all.
 *
 * By default an instance is built as `new static($parsedValue)`; a class whose
 * constructor takes something else overrides {@see fromParsed()}.
 */
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
     * Returns the schema wrapped so that parsing yields an instance of this class.
     *
     * The wrapper parses through {@see getSchema()} and then calls
     * {@see fromParsed()}, so validation, normalisation and error paths are
     * exactly those of the plain schema; only the result differs. The wrapper's
     * own `optional()`, `nullable()` and `default()` apply before construction,
     * so an absent or null field stays absent or null.
     *
     * Call this on a concrete class: an abstract base cannot be instantiated.
     */
    public static function getValueObjectSchema(): SanitizrValueObject
    {
        return new SanitizrValueObject(static::class, static::getSchema(), static::fromParsed(...));
    }

    /**
     * Parses the input and returns it as an instance of this class.
     *
     * @throws SanitizrValidationException If the input does not satisfy the schema.
     */
    public static function from(mixed $value): static
    {
        return static::getValueObjectSchema()->parse($value);
    }

    /**
     * Like {@see from()}, but answers null instead of throwing for an invalid input.
     */
    public static function tryFrom(mixed $value): ?static
    {
        $parsed = static::getValueObjectSchema()->safeParse($value);

        return $parsed->isValid() ? $parsed->getValue() : null;
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

    /**
     * Builds the instance from a value the schema has already accepted.
     *
     * The default hands the parsed value to the constructor as its single
     * argument, which fits a class wrapping one scalar. Override it when the
     * constructor wants the value taken apart — an object schema yielding an
     * array, say — and keep it protected: the schema is the only door in.
     *
     * @param mixed $value The parsed, normalised value.
     */
    protected static function fromParsed(mixed $value): static
    {
        return new static($value);
    }

    abstract protected static function defineSchema(): AbstractSanitizrSchema;
}
