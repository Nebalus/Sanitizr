<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrObject extends AbstractSanitizrSchema
{
    /**
     * Initializes the SanitizrObject with a set of property schemas.
     *
     * @param array $schemas An associative array mapping property names to their corresponding schemas.
     */
    public function __construct(
        private readonly array $schemas
    ) {
    }

    /**
     * Parses and validates an input value as an object or associative array according to the defined schemas.
     *
     * Converts input objects to associative arrays, checks for required and optional properties, applies default values, and recursively parses nested objects. Throws a SanitizrValidationException if the input is not a valid object/array or if required properties are missing.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $message Error message template for invalid input types.
     * @param string $path The current property path for error reporting in nested structures.
     * @return array The parsed and validated associative array.
     * @throws SanitizrValidationException If the input is not an object/associative array or required properties are missing.
     */
    protected function parseValue(mixed $input, string $message = '%s must be an OBJECT or an ASSOCIATIVE ARRAY', string $path = ''): array
    {
        if (is_object($input)) {
            $input = get_object_vars($input);
        }

        if (!is_array($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        $result = [];

        foreach ($this->schemas as $prop => $schema) {
            if ($schema instanceof AbstractSanitizrSchema) {
                $updatedPath = $path === '' ? $prop : $path . "." . $prop;

                if ($schema->hasDefaultValue() && array_key_exists($prop, $input) === false) {
                    $result[$prop] = $schema->getDefaultValue();
                    continue;
                }

                if ($schema->isOptional() === false && array_key_exists($prop, $input) === false) {
                    throw new SanitizrValidationException($updatedPath . " is required");
                }

                if ($schema->isOptional() === true && array_key_exists($prop, $input) === false) {
                    continue;
                }

                if ($schema instanceof SanitizrObject) {
                    if (array_key_exists($prop, $input)) {
                        $result[$prop] = $schema->parseValue(
                            $input[$prop],
                            path: $updatedPath
                        );
                    }
                    continue;
                }

                $result[$prop] = $schema->parse(
                    $input[$prop] ?? null,
                    path: $updatedPath
                );
            }
        }

        return $result;
    }
}
