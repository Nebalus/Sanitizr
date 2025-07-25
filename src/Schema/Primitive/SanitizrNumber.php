<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;
use Nebalus\Sanitizr\Type\SanitizrErrorMessage;

class SanitizrNumber extends AbstractSanitizrSchema
{
    use SchemaStringableTrait;

    public function gt(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_GREATER_THAN): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input < $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function gte(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_GREATER_THAN_OR_EQUAL): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input <= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function lt(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_LESS_THAN): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input > $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function lte(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_LESS_THAN_OR_EQUAL): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input >= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function float(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_FLOAT): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if (! is_float($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function integer(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_INTEGER): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if (! is_int($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function positive(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_POSITIVE): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input <= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function nonPositive(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_NONPOSITIVE): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input > 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function negative(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_NEGATIVE): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input >= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function nonNegative(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_NONNEGATIVE): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input < 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function multipleOf(int|float $multiple, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_MULTIPLE_OF): static
    {
        $this->addCheck(function (int|float $input) use ($multiple, $message) {
            if ($input % $multiple != 0) {
                throw new SanitizrValidationException(sprintf($message, $multiple));
            }
        });

        return $this;
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = SanitizrErrorMessage::VALUE_MUST_BE_NUMERIC, string $path = ''): int
    {
        if ($this->isStringable) { // TODO Check if an the Number is a float or an integer
            $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_NULL_ON_FAILURE);
        }

        if (! is_numeric($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
