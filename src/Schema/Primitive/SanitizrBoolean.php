<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;
use Nebalus\Sanitizr\Type\SanitizrErrorMessage;

class SanitizrBoolean extends AbstractSanitizrSchema
{
    use SchemaStringableTrait;

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = SanitizrErrorMessage::VALUE_MUST_BE_BOOLEAN, string $path = ''): bool
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
