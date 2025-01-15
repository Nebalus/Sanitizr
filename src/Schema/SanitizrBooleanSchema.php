<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrBooleanSchema extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue($input): bool
    {
        if (! is_bool($input)) {
            throw new SanitizrValidationException('Not a boolean value');
        }

        return $input;
    }
}
