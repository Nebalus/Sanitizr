<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrBooleanSchema extends AbstractSanitizrSchema
{
    public function equals(bool $value, string $message = 'Is not equals to the required boolean %s'): static
    {
        $this->addCheck(function (bool $input) use ($value, $message) {
            if ($input != $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = '%s must be an BOOLEAN', string $path = ''): bool
    {
        // TODO: Make this optional
        $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (! is_bool($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
