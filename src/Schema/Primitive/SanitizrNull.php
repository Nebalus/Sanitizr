<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

class SanitizrNull extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = '%s must be NULL', string $path = ''): null
    {
        if (! is_null($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return null;
    }
}
