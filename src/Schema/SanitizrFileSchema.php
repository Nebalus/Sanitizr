<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrFileSchema extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = "%s must be a path to an FILE", string $path = ''): mixed
    {
        // TODO: Implement parseValue() method.
    }
}
