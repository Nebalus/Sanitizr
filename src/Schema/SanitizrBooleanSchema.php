<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrBooleanSchema extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = 'Value must be an BOOLEAN', string $path = ''): bool
    {
        if (! is_bool($input)) {
            throw new SanitizrValidationException($message);
        }

        return $input;
    }
}
