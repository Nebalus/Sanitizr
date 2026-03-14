<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;

class SanitizrBoolean extends AbstractSanitizrSchema
{
    use SchemaStringableTrait;

    /**
     * Parses and validates the input as a boolean value.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $path Optional path context for error reporting.
     * @return bool The validated boolean value.
     * @throws SanitizrValidationException If the input cannot be parsed as a boolean.
     */
    protected function parseValue(mixed $input, string $path = ''): bool
    {
        if ($this->isStringable) {
            $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (! is_bool($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: sprintf("%s must be a BOOLEAN", $path !== '' ? $path : 'Value'),
                expected: 'boolean',
                received: gettype($input),
            ));
        }

        return $input;
    }
}
