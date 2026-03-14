<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

class SanitizrString extends AbstractSanitizrSchema
{
    /****
     * Adds a validation rule that requires the string to have an exact length.
     *
     * @param int $length The required length of the string.
     * @param string|null $message Optional custom error message if validation fails.
     * @return static The current schema instance for method chaining.
     */
    public function length(int $length, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($length, $message) {
            $inputLength = strlen($input);

            if ($inputLength !== $length) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be exact %s characters long", $length),
                    expected: "length:$length",
                    received: "length:$inputLength",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to have at least the specified minimum length.
     *
     * @param int $min The minimum allowed length for the string.
     * @param string|null $message Optional custom error message if validation fails.
     * @return static The current schema instance for method chaining.
     */
    public function min(int $min, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($min, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_SMALL,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be %s or more characters long", $min),
                    expected: "min:$min",
                    received: "length:$inputLength",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string does not exceed the specified maximum length.
     *
     * @param int $max The maximum allowed length for the string.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function max(int $max, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($max, $message) {
            $inputLength = strlen($input);

            if ($inputLength > $max) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be %s or fewer characters long", $max),
                    expected: "max:$max",
                    received: "length:$inputLength",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string length to be within the specified inclusive range.
     *
     * @param int $min The minimum allowed string length.
     * @param int $max The maximum allowed string length.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function between(int $min, int $max, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($min, $max, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min || $inputLength > $max) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: $inputLength < $min ? SanitizrIssue::TOO_SMALL : SanitizrIssue::TOO_BIG,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf("Must be between %s and %s characters long", $min, $max),
                    expected: "between:$min:$max",
                    received: "length:$inputLength",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to be entirely uppercase.
     *
     * @param string|null $message Optional custom error message.
     * @return static The current schema instance for method chaining.
     */
    public function uppercase(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if ($input !== strtoupper($input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be uppercase",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to be entirely lowercase.
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function lowercase(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if ($input !== strtolower($input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be lowercase",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to contain the specified substring.
     *
     * @param string $needle The substring that must be present in the input string.
     * @param string|null $message Optional custom error message.
     * @return static The current schema instance for method chaining.
     */
    public function includes(string $needle, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($needle, $message) {
            if (!str_contains($input, $needle)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? sprintf('Must include "%s"', $needle),
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to match the given regular expression pattern.
     *
     * @param string $pattern The regular expression pattern to match.
     * @param string|null $message Optional custom error message.
     * @return static The current schema instance for method chaining.
     */
    public function regex(string $pattern, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($pattern, $message) {
            if (! preg_match($pattern, $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Does not match the pattern",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string is a valid email address.
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function email(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Not a valid email address",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to be a valid URL.
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function url(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_URL)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Not a valid URL",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string is a valid phone number.
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function phone(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^(?=.*[0-9])\+?[0-9\s\-\(\)]{7,20}$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Not a valid phone number",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to start with the specified prefix.
     *
     * @param string $prefix The required starting substring.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function startsWith(string $prefix, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($prefix, $message) {
            if (str_starts_with($input, $prefix) === false) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Does not start with required string prefix",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to end with the specified suffix.
     *
     * @param string $suffix The required ending substring.
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function endsWith(string $suffix, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($suffix, $message) {
            if (str_ends_with($input, $suffix) === false) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Does not end with required string suffix",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string contains only alphanumeric characters.
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function alphanumeric(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! ctype_alnum($input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Must be alphanumeric",
                ));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string contains only digits.
     *
     * @param string|null $message Optional custom error message.
     * @return static
     */
    public function digits(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! ctype_digit($input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Must contain only digits",
                ));
            }
        });

        return $newSchema;
    }


    /**
     * Adds a transformation to trim whitespace from both ends of the string.
     *
     * @return static The current schema instance for method chaining.
     */
    public function trim(): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform(function (string $input): string {
            return trim($input);
        });

        return $newSchema;
    }

    /**
     * Adds a transformation to convert the string to lowercase.
     *
     * @return static The current schema instance for method chaining.
     */
    public function toLowerCase(): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform(function (string $input): string {
            return strtolower($input);
        });

        return $newSchema;
    }

    /**
     * Adds a transformation to convert the string to uppercase.
     *
     * @return static The current schema instance for method chaining.
     */
    public function toUpperCase(): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform(function (string $input): string {
            return strtoupper($input);
        });

        return $newSchema;
    }

    /**
     * Adds a transformation to convert the string to title case, capitalizing the first letter of each word.
     *
     * @return static The current schema instance for method chaining.
     */
    public function toTitleCase(): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform(function (string $input): string {
            return ucwords(strtolower($input));
        });

        return $newSchema;
    }

    /**
     * Adds a transformation to remove HTML and PHP tags from the string, optionally allowing specified tags.
     *
     * @param string|null $allowedTags A string of tags to allow (e.g., '<b><i>'), or null to strip all tags.
     * @return static The current schema instance for method chaining.
     * @deprecated [v1.0.1] [Use `strip_tags` instead of `stripTags` for consistency with PHP's built-in function.]
     * @see strip_tags()
     * @see https://www.php.net/manual/en/function.strip-tags.php
     */
    public function stripTags($allowedTags = null): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform(function (string $input) use ($allowedTags): string {
            return strip_tags($input, $allowedTags);
        });

        return $newSchema;
    }

    /**
     * Adds a transformation to convert special characters in the string to HTML entities.
     *
     * @param int $flags Optional flags for htmlspecialchars. Defaults to ENT_QUOTES | ENT_SUBSTITUTE.
     * @param string|null $encoding Optional character encoding. If null, the default encoding is used.
     * @param bool $doubleEncode Whether to convert existing HTML entities. Defaults to true.
     * @return static The current schema instance for method chaining.
     * @deprecated [v1.0.1] [Use `htmlspecialchars` instead of `htmlSpecialChars` for consistency with PHP's built-in function.]
     * @see htmlspecialchars()
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public function htmlSpecialChars(int $flags = ENT_QUOTES | ENT_SUBSTITUTE, ?string $encoding = null, bool $doubleEncode = true): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform(function (string $input) use ($doubleEncode, $encoding, $flags): string {
            return htmlspecialchars($input, $flags, $encoding, $doubleEncode);
        });

        return $newSchema;
    }


    /**
     * Ensures the input is a string, throwing a SanitizrValidationException if not.
     *
     * @param mixed $input The value to validate as a string.
     * @param string $path The path or field name for error reporting.
     * @return string The validated string input.
     * @throws SanitizrValidationException If the input is not a string.
     */
    protected function parseValue(mixed $input, string $path = ''): string
    {
        if (!is_string($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be a STRING",
                expected: 'string',
                received: gettype($input),
            ));
        }

        return $input;
    }
}
