<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrNullSchema extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = 'Value must be NULL'): null
    {
        if (! is_null($input)) {
            throw new SanitizrValidationException($message);
        }

        return null;
    }
}
