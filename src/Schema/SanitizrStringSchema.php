<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrStringSchema extends AbstractSanitizrSchema
{
    /**
     * @param int $length The string must be the exact length
     */
    public function length(int $length, string $message = 'Must be exact %s characters long'): static
    {
        $this->addCheck(function (string $input) use ($length, $message) {
            $inputLength = strlen($input);

            if ($inputLength !== $length) {
                throw new SanitizrValidationException(sprintf($message, $length));
            }
        });

        return $this;
    }

    public function min(int $min, string $message = 'Must be %s or more characters long'): static
    {
        $this->addCheck(function (string $input) use ($min, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min) {
                throw new SanitizrValidationException(sprintf($message, $min));
            }
        });

        return $this;
    }

    public function max(int $max, string $message = 'Must be %s or fewer characters long'): static
    {
        $this->addCheck(function (string $input) use ($max, $message) {
            $inputLength = strlen($input);

            if ($inputLength > $max) {
                throw new SanitizrValidationException(sprintf($message, $max));
            }
        });

        return $this;
    }

    public function between(int $min, int $max, string $message = 'Must be between %s and %s'): static
    {
        $this->addCheck(function (string $input) use ($min, $max, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min || $inputLength > $max) {
                throw new SanitizrValidationException(sprintf($message, $min, $max));
            }
        });

        return $this;
    }


    public function regex(string $pattern, string $message = 'Does not match the pattern'): static
    {
        $this->addCheck(function (string $input) use ($pattern, $message) {
            if (! preg_match($pattern, $input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function email(string $message = 'Not a valid email address'): static
    {
        $this->addCheck(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function url(string $message = 'Not a valid URL'): static
    {
        $this->addCheck(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_URL)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function equals(string $value, string $message = 'Is not equals to the required string'): static
    {
        $this->addCheck(function (string $input) use ($value, $message) {
            if (strcmp($input, $value) !== 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function startsWith(string $prefix, string $message = 'Does not start with required string'): static
    {
        $this->addCheck(function (string $input) use ($prefix, $message) {
            if (str_starts_with($input, $prefix) === false) {
                throw new SanitizrValidationException(sprintf($message, $prefix));
            }
        });

        return $this;
    }

    public function endsWith(string $suffix, string $message = 'Does not end with required string'): static
    {
        $this->addCheck(function (string $input) use ($suffix, $message) {
            if (str_ends_with($input, $suffix) === false) {
                throw new SanitizrValidationException(sprintf($message, $suffix));
            }
        });

        return $this;
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = '%s must be a STRING', string $path = ''): string
    {
        if (! is_string($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
