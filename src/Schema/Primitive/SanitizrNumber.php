<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;
use Nebalus\Sanitizr\Type\SanitizrErrorMessage;

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
    public function gt(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_GREATER_THAN): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($value, $message) {
            if ($input < $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $newSchema;
    }

    public function gte(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_GREATER_THAN_OR_EQUAL): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($value, $message) {
            if ($input <= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $newSchema;
    }

    public function lt(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_LESS_THAN): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($value, $message) {
            if ($input > $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $newSchema;
    }

    public function lte(int|float $value, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_LESS_THAN_OR_EQUAL): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($value, $message) {
            if ($input >= $value) {
                throw new SanitizrValidationException(sprintf($message, $value));
            }
        });

        return $newSchema;
    }

    public function float(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_FLOAT): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($message) {
            if (! is_float($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    public function integer(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_INTEGER): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($message) {
            if (! is_int($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    public function positive(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_POSITIVE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($message) {
            if ($input <= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    public function nonPositive(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_NONPOSITIVE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($message) {
            if ($input > 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    public function negative(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_NEGATIVE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($message) {
            if ($input >= 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the input to be greater than or equal to zero.
     *
     * @param string $message Custom error message if the input is negative.
     * @return static
     */
    public function nonNegative(string $message = SanitizrErrorMessage::NUMBER_MUST_BE_NONNEGATIVE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($message) {
            if ($input < 0) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the input to be greater than or equal to zero.
     *
     * @param string $message Custom error message if the input is negative.
     * @return static
     */
    public function multipleOf(int|float $multiple, string $message = SanitizrErrorMessage::NUMBER_MUST_BE_MULTIPLE_OF): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input) use ($multiple, $message) {
            if ($input % $multiple != 0) {
                throw new SanitizrValidationException(sprintf($message, $multiple));
            }
        });

        return $newSchema;
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
