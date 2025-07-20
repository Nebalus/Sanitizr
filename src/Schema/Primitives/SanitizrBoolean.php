<?php

namespace Nebalus\Sanitizr\Schema\Primitives;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;

class SanitizrBoolean extends AbstractSanitizrSchema
{
    use SchemaStringableTrait;

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = '%s must be an BOOLEAN', string $path = ''): bool
    {
        if ($this->isStringable) {
            $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (! is_bool($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
