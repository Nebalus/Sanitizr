<?php

namespace Nebalus\Sanitizr\Schema;

use Closure;
use LogicException;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

/**
 * A schema that yields an instance of a class instead of the parsed value.
 *
 * Wraps the schema that validates and normalises the raw input, plus a factory
 * that turns the parsed value into the object. Because the factory runs inside
 * {@see parseValue()}, the object is produced wherever this node sits in a
 * larger schema — as an object property, an array item, a union or tuple
 * member — and the node's own `optional()`, `nullable()` and `default()` are
 * answered before the factory is ever called, so a missing or null field never
 * reaches the constructor.
 *
 * Normally obtained from {@see \Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait::getValueObjectSchema()}
 * rather than built by hand.
 */
final class SanitizrValueObject extends AbstractSanitizrSchema
{
    /**
     * @param class-string $class The class every parse yields.
     * @param AbstractSanitizrSchema $schema Validates and normalises the raw input.
     * @param Closure(mixed): object $factory Builds the instance from the parsed value.
     */
    public function __construct(
        private readonly string $class,
        private readonly AbstractSanitizrSchema $schema,
        private readonly Closure $factory,
    ) {
    }

    /**
     * @return class-string The class this schema yields.
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * The schema the raw input is parsed with before the factory runs.
     */
    public function getInnerSchema(): AbstractSanitizrSchema
    {
        return $this->schema;
    }

    /**
     * @throws SanitizrValidationException If the input does not satisfy the inner schema.
     * @throws LogicException If the factory hands back something other than the declared class.
     */
    protected function parseValue(mixed $input, string $path = ''): object
    {
        $object = ($this->factory)($this->schema->parse($input, path: $path));

        if (!is_a($object, $this->class)) {
            throw new LogicException(sprintf(
                'Value object factory for %s returned %s',
                $this->class,
                get_debug_type($object),
            ));
        }

        return $object;
    }

    /**
     * A value object cannot be encoded: the factory has no inverse, just like a transform.
     *
     * @throws LogicException Always.
     */
    protected function encodeValue(mixed $input, string $path = ''): never
    {
        throw new LogicException(sprintf(
            'Encountered unidirectional value object %s during encode: use a codec to make the conversion reversible',
            $this->class,
        ));
    }
}
