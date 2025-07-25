<?php

namespace Nebalus\Sanitizr\Schema\Primitives;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;

class SanitizrBoolean extends AbstractSanitizrSchema
{
    use SchemaStringableTrait;

    /**
     * Parses and validates the input as a boolean value.
     *
     * Converts the input to a boolean if stringable, and ensures the result is a boolean. Throws a SanitizrValidationException if validation fails.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $message Optional error message template for validation failure.
     * @param string $path Optional path context for error reporting.
     * @return bool The validated boolean value.
     * @throws SanitizrValidationException If the input cannot be parsed as a boolean.
     */
    protected function parseValue(mixed $input, string $message = '%s must be an BOOLEAN', string $path = ''): bool
    {
        if ($this->isStringable) {
            $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (! is_bool($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
