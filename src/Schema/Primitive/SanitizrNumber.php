<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Error\SanitizrIssue;
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
     * @param string|null $message Optional custom error message.
     * @return static The current schema instance for method chaining.
     */
    public function gt(int|float $value, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($value, $message) {
            if ($input <= $value) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be greater than %s", $value),
                    expected: "gt:$value",
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    public function gte(int|float $value, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($value, $message) {
            if ($input < $value) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be greater than or equal to %s", $value),
                    expected: "gte:$value",
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    public function lt(int|float $value, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($value, $message) {
            if ($input >= $value) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be less than %s", $value),
                    expected: "lt:$value",
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    public function lte(int|float $value, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($value, $message) {
            if ($input > $value) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be less than or equal to %s", $value),
                    expected: "lte:$value",
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    public function float(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($message) {
            if (! is_float($input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_TYPE,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be a float number",
                    expected: 'float',
                    received: gettype($input),
                ));
            }
        });

        return $newSchema;
    }

    public function integer(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($message) {
            if (! is_int($input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_TYPE,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be an integer number",
                    expected: 'integer',
                    received: gettype($input),
                ));
            }
        });

        return $newSchema;
    }

    public function positive(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($message) {
            if ($input <= 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be a positive number",
                    expected: 'gt:0',
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    public function nonPositive(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($message) {
            if ($input > 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be a negative number or 0",
                    expected: 'lte:0',
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    public function negative(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($message) {
            if ($input >= 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be a negative number",
                    expected: 'lt:0',
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the input to be greater than or equal to zero.
     *
     * @param string|null $message Custom error message if the input is negative.
     * @return static
     */
    public function nonNegative(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($message) {
            if ($input < 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be a positive number or 0",
                    expected: 'gte:0',
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the input to be a multiple of the specified value.
     *
     * @param int|float $multiple The value that the input must be a multiple of.
     * @param string|null $message Custom error message if the input is not a multiple.
     * @return static
     */
    public function multipleOf(int|float $multiple, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (int|float $input, string $path) use ($multiple, $message) {
            if ($input % $multiple != 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::NOT_MULTIPLE_OF,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be a multiple of %s", $multiple),
                    expected: "multiple_of:$multiple",
                    received: (string) $input,
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Parses and validates that the input is numeric, optionally sanitizing stringable input.
     *
     * @param mixed $input The value to validate as numeric.
     * @param string $path The path or label to include in the error message.
     * @return int|float The validated numeric value.
     * @throws SanitizrValidationException If the input is not numeric.
     */
    protected function parseValue(mixed $input, string $path = ''): int|float
    {
        if ($this->isStringable) { // TODO Check if an the Number is a float or an integer
            $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_NULL_ON_FAILURE);
        }

        if (! is_numeric($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be NUMERIC",
                expected: 'number',
                received: gettype($input),
            ));
        }

        return $input;
    }
}
