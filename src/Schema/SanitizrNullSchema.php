<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrNullSchema extends AbstractSanitizrSchema
{
    protected function parseValue(mixed $input): null
    {
        if (! is_null($input)) {
            throw new SanitizrValidationException('Value must be null');
        }

        return null;
    }
}
