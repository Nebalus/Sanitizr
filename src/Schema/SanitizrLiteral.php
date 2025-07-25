<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrLiteral extends AbstractSanitizrSchema
{
    /**
     * Initializes the schema with a literal value or set of values to validate against.
     *
     * @param mixed $literalValue The literal value or array of values that input must match.
     */
    public function __construct(
        private readonly mixed $literalValue
    ) {
    }

    /**
     * Validates that the input matches the stored literal value or one of a set of literal values.
     *
     * If the stored literal value is a single value, the input must strictly equal it. If the stored literal value is an array, the input must loosely equal one of its elements. Throws a SanitizrValidationException if the input does not match.
     *
     * @param mixed $input The value to validate.
     * @param string $message Optional error message template with placeholders for the path and expected value.
     * @param string $path Optional input path for error reporting.
     * @return mixed The validated input if it matches the literal value or one of the allowed values.
     * @throws SanitizrValidationException If the input does not match the expected literal value(s).
     */
    protected function parseValue(mixed $input, string $message = '%s must be literally "%s"', string $path = ''): mixed
    {
        if ($this->literalValue === $input) {
            return $input;
        }

        if (is_array($this->literalValue)) {
            foreach ($this->literalValue as $value) {
                if ($value == $input) {
                    return $input;
                }
            }
        }

        throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value', $this->literalValue));
    }
}
