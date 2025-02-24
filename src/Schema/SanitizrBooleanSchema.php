<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrBooleanSchema extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = 'Not a boolean value'): bool
    {
        if (! is_bool($input)) {
            throw new SanitizrValidationException($message);
        }

        return $input;
    }
}
