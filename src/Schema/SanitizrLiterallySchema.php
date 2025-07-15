<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrLiterallySchema extends AbstractSanitizrSchema
{
    public function __construct(
        private mixed $literallyValue
    ) {
    }

    protected function parseValue(mixed $input, string $message = '%s must be literally "%s"', string $path = ''): mixed
    {
        if (! $this->literallyValue === $input) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value', $this->literallyValue));
        }

        return $input;
    }
}
