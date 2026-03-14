<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrIssue;
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
     * Retrieves the literal value configured for this schema.
     *
     * @return mixed The configured literal value.
     */
    public function getLiteralValue(): mixed
    {
        return $this->literalValue;
    }

    /**
     * Validates that the input matches the stored literal value or one of a set of literal values.
     *
     * @param mixed $input The value to validate.
     * @param string $path Optional input path for error reporting.
     * @return mixed The validated input if it matches the literal value or one of the allowed values.
     * @throws SanitizrValidationException If the input does not match the expected literal value(s).
     */
    protected function parseValue(mixed $input, string $path = ''): mixed
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

        $expectedStr = is_scalar($this->literalValue)
            ? (string) $this->literalValue
            : (is_array($this->literalValue)
                ? implode(', ', array_map(fn($v) => is_scalar($v) ? (string) $v : gettype($v), $this->literalValue))
                : gettype($this->literalValue));

        throw SanitizrValidationException::fromIssue(new SanitizrIssue(
            code: SanitizrIssue::INVALID_LITERAL,
            path: self::pathToArray($path),
            message: sprintf('%s must be literally "%s"', $path !== '' ? $path : 'Value', $expectedStr),
            expected: $expectedStr,
            received: is_scalar($input) ? (string) $input : gettype($input),
        ));
    }
}
