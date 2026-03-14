<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrEnum extends AbstractSanitizrSchema
{
    /**
     * Initializes the schema with a set of allowed literal values.
     *
     * @param array $values The array of allowed values that input must match.
     */
    public function __construct(
        private readonly array $values
    ) {
    }

    /**
     * Retrieves the allowed values configured for this schema.
     *
     * @return array The allowed values.
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Validates that the input matches one of the allowed values.
     *
     * @param mixed $input The value to validate.
     * @param string $path Optional input path for error reporting.
     * @return mixed The validated input if it matches one of the allowed values.
     * @throws SanitizrValidationException If the input does not match any allowed value.
     */
    protected function parseValue(mixed $input, string $path = ''): mixed
    {
        foreach ($this->values as $value) {
            if ($value === $input) {
                return $input;
            }
        }

        $allowedValuesString = implode(', ', array_map(fn($v) => is_scalar($v) ? (string)$v : gettype($v), $this->values));

        throw SanitizrValidationException::fromIssue(new SanitizrIssue(
            code: SanitizrIssue::INVALID_ENUM_VALUE,
            path: self::pathToArray($path),
            message: sprintf("Value must be one of: [%s]", $allowedValuesString),
            expected: $allowedValuesString,
            received: is_scalar($input) ? (string) $input : gettype($input),
        ));
    }
}
