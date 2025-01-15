<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

class SanitizrNumberSchema extends AbstractSanitizrSchema
{
    public function gt(int|float $value, string $message = 'Must be greater than %s'): static
    {
        $this->addEffect(function (int|float $input) use ($value, $message) {
            if ($input < $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function gte(int|float $value, string $message = 'Must be greater than or equal to %s'): static
    {
        $this->addEffect(function (int|float $input) use ($value, $message) {
            if ($input <= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function lt(int|float $value, string $message = 'Must be less than %s'): static
    {
        $this->addEffect(function (int|float $input) use ($value, $message) {
            if ($input > $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function lte(int|float $value, string $message = 'Must be less than or equal to %s'): static
    {
        $this->addEffect(function (int|float $input) use ($value, $message) {
            if ($input >= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function float(string $message = 'Must be an float number'): static
    {
        $this->addEffect(function (int|float $input) use ($message) {
            if (! is_float($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function integer(string $message = 'Must be an integer number'): static
    {
        $this->addEffect(function (int|float $input) use ($message) {
            if (! is_int($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function positive(string $message = 'Must be a positive number'): static
    {
        $this->addEffect(function (int|float $input) use ($message) {
            if ($input <= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function nonpositive(string $message = 'Must be a negative number or 0'): static
    {
        $this->addEffect(function (int|float $input) use ($message) {
            if ($input > 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function negative(string $message = 'Must be a negative number'): static
    {
        $this->addEffect(function (int|float $input) use ($message) {
            if ($input >= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function nonnegative(string $message = 'Must be a positive number or 0'): static
    {
        $this->addEffect(function (int|float $input) use ($message) {
            if ($input < 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function multipleOf(int $multiple, string $message = 'Must be a multiple of %s'): static
    {
        $this->addEffect(function (int $input) use ($multiple, $message) {
            if ($input % $multiple !== 0) {
                throw new SanitizrValidationException(sprintf($message, $multiple));
            }
        });

        return $this;
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input): int
    {
        if (! is_numeric($input)) {
            throw new SanitizrValidationException('Not a numeric value');
        }

        return $input;
    }
}
