<?php

namespace Nebalus\Sanitizr\Schema\Primitives;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;

class SanitizrNumber extends AbstractSanitizrSchema
{
    use SchemaStringableTrait;

    /**
     * Adds a validation rule that requires the input to be strictly greater than the specified value.
     *
     * @param int|float $value The value that the input must be greater than.
     * @param string $message Optional custom error message. Use '%s' as a placeholder for the value.
     * @return static The current schema instance for method chaining.
     */
    public function gt(int|float $value, string $message = 'Must be greater than %s'): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input < $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function gte(int|float $value, string $message = 'Must be greater than or equal to %s'): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input <= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function lt(int|float $value, string $message = 'Must be less than %s'): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input > $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function lte(int|float $value, string $message = 'Must be less than or equal to %s'): static
    {
        $this->addCheck(function (int|float $input) use ($value, $message) {
            if ($input >= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $this;
    }

    public function float(string $message = 'Must be an float number'): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if (! is_float($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function integer(string $message = 'Must be an integer number'): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if (! is_int($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function positive(string $message = 'Must be a positive number'): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input <= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function nonPositive(string $message = 'Must be a negative number or 0'): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input > 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function negative(string $message = 'Must be a negative number'): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input >= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    /**
     * Adds a validation rule that requires the input to be greater than or equal to zero.
     *
     * @param string $message Custom error message if the input is negative.
     * @return static
     */
    public function nonNegative(string $message = 'Must be a positive number or 0'): static
    {
        $this->addCheck(function (int|float $input) use ($message) {
            if ($input < 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    /**
     * Adds a validation rule that requires the input to be a multiple of the specified number.
     *
     * @param int|float $multiple The number that the input must be a multiple of.
     * @param string $message The error message to use if validation fails.
     * @return static The current schema instance for method chaining.
     */
    public function multipleOf(int|float $multiple, string $message = 'Must be a multiple of %s'): static
    {
        $this->addCheck(function (int|float $input) use ($multiple, $message) {
            if ($input % $multiple != 0) {
                throw new SanitizrValidationException(sprintf($message, $multiple));
            }
        });

        return $this;
    }

    /**
     * Parses and validates that the input is numeric, optionally sanitizing stringable input.
     *
     * If the input is not numeric after optional sanitization, throws a SanitizrValidationException with a formatted message.
     *
     * @param mixed $input The value to validate as numeric.
     * @param string $message The error message template, with a placeholder for the path or value.
     * @param string $path The path or label to include in the error message.
     * @return int|float The validated numeric value.
     * @throws SanitizrValidationException If the input is not numeric.
     */
    protected function parseValue(mixed $input, string $message = '%s must be NUMERIC', string $path = ''): int
    {
        if ($this->isStringable) {
            $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_NULL_ON_FAILURE);
        }

        if (! is_numeric($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
