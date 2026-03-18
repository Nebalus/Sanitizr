<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrArray extends AbstractSanitizrSchema
{
    /**
     * Initializes the SanitizrArray with a schema for validating each array element.
     *
     * @param AbstractSanitizrSchema $schema The schema used to validate and parse each element of the array.
     */
    public function __construct(
        private readonly AbstractSanitizrSchema $schema
    ) {
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $path = ''): array
    {
        if (! is_array($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be an ARRAY",
                expected: 'array',
                received: gettype($input),
            ));
        }

        $result = [];
        $collectedErrors = new SanitizrError();

        foreach ($input as $index => $v) {
            $updatedPath = $path === '' ? (string) $index : $path . '.' . $index;
            try {
                $result[] = $this->schema->parse($v, path: $updatedPath);
            } catch (SanitizrValidationException $e) {
                $collectedErrors->merge($e->getError());
            }
        }

        if ($collectedErrors->hasIssues()) {
            throw SanitizrValidationException::fromError($collectedErrors);
        }

        return $result;
    }
    /**
     * Adds a validation rule that requires the array to have at least the specified minimum number of elements.
     *
     * @param int $min The minimum allowed number of elements.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function min(int $min, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (array $input, string $path) use ($min, $message) {
            $count = count($input);

            if ($count < $min) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Array must contain at least %s element(s)", $min),
                    expected: "min:$min",
                    received: "length:$count",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the array does not exceed the specified maximum number of elements.
     *
     * @param int $max The maximum allowed number of elements.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function max(int $max, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (array $input, string $path) use ($max, $message) {
            $count = count($input);

            if ($count > $max) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Array must contain at most %s element(s)", $max),
                    expected: "max:$max",
                    received: "length:$count",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the array length to be within the specified inclusive range.
     *
     * @param int $min The minimum allowed number of elements.
     * @param int $max The maximum allowed number of elements.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function between(int $min, int $max, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (array $input, string $path) use ($min, $max, $message) {
            $count = count($input);

            if ($count < $min || $count > $max) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: $count < $min ? SanitizrIssue::TOO_SMALL : SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Array must contain between %s and %s element(s)", $min, $max),
                    expected: "between:$min:$max",
                    received: "length:$count",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the array to be not empty (length > 0).
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function notEmpty(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (array $input, string $path) use ($message) {
            $count = count($input);

            if ($count === 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? "Array cannot be empty",
                    expected: "min:1",
                    received: "length:0",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the array to be empty (length === 0).
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function empty(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (array $input, string $path) use ($message) {
            $count = count($input);

            if ($count !== 0) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? "Array must be empty",
                    expected: "max:0",
                    received: "length:$count",
                ));
            }
        });

        return $newSchema;
    }
}
