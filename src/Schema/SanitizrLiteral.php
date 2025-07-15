<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrLiteral extends AbstractSanitizrSchema
{
    public function __construct(
        private readonly mixed $literalValue
    ) {
    }

    protected function parseValue(mixed $input, string $message = '%s must be literally "%s"', string $path = ''): mixed
    {
        if (! $this->literalValue === $input) {
            return $input;
        }

        if (is_array($this->literalValue)) {
            foreach ($this->literalValue as $value) {
                if ($value == $input) {
                    return $input;
                }
            }
        }

        throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value', $this->literalValue));
    }
}
