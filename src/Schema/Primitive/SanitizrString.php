<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Type\SanitizrErrorMessage;
use Nebalus\Sanitizr\Types\SanitizrErrorMessages;

class SanitizrString extends AbstractSanitizrSchema
{
    /****
     * Adds a validation rule that requires the string to have an exact length.
     *
     * @param int $length The required length of the string.
     * @param string $message Optional custom error message if validation fails.
     * @return static The current schema instance for method chaining.
     */
    public function length(int $length, string $message = SanitizrErrorMessage::STRING_LENGTH): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($length, $message) {
            $inputLength = strlen($input);

            if ($inputLength !== $length) {
                throw new SanitizrValidationException(sprintf($message, $length));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to have at least the specified minimum length.
     *
     * If the string is shorter than the given minimum, a SanitizrValidationException is thrown with the provided error message.
     *
     * @param int $min The minimum allowed length for the string.
     * @param string $message The error message to use if validation fails. The placeholder `%s` will be replaced with the minimum length.
     * @return static The current schema instance for method chaining.
     */
    public function min(int $min, string $message = SanitizrErrorMessages::STRING_MIN_LENGTH): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($min, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min) {
                throw new SanitizrValidationException(sprintf($message, $min));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string does not exceed the specified maximum length.
     *
     * Throws a SanitizrValidationException if the string's length is greater than the given maximum.
     *
     * @param int $max The maximum allowed length for the string.
     * @param string $message Optional custom error message, with `%s` replaced by the maximum length.
     * @return static
     */
    public function max(int $max, string $message = SanitizrErrorMessages::STRING_MAX_LENGTH): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($max, $message) {
            $inputLength = strlen($input);

            if ($inputLength > $max) {
                throw new SanitizrValidationException(sprintf($message, $max));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string length to be within the specified inclusive range.
     *
     * Throws a SanitizrValidationException if the string length is less than $min or greater than $max.
     *
     * @param int $min The minimum allowed string length.
     * @param int $max The maximum allowed string length.
     * @param string $message The error message to use if validation fails. Supports sprintf placeholders for $min and $max.
     * @return static
     */
    public function between(int $min, int $max, string $message = SanitizrErrorMessages::STRING_BETWEEN_RANGE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($min, $max, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min || $inputLength > $max) {
                throw new SanitizrValidationException(sprintf($message, $min, $max));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to be entirely uppercase.
     *
     * @param string $message The error message to use if the validation fails.
     * @return static The current schema instance for method chaining.
     */
    public function uppercase(string $message = SanitizrErrorMessages::STRING_ONLY_UPPERCASE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($message) {
            if ($input !== strtoupper($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to be entirely lowercase.
     *
     * If the input string contains any uppercase characters, a SanitizrValidationException is thrown with the provided message.
     *
     * @param string $message Custom error message for validation failure.
     * @return static
     */
    public function lowercase(string $message = SanitizrErrorMessages::STRING_ONLY_LOWERCASE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($message) {
            if ($input !== strtolower($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to contain the specified substring.
     *
     * @param string $needle The substring that must be present in the input string.
     * @param string $message Optional custom error message. The substring will be injected via sprintf.
     * @return static The current schema instance for method chaining.
     * @throws SanitizrValidationException If the input string does not contain the specified substring.
     */
    public function includes(string $needle, string $message = SanitizrErrorMessages::STRING_MUST_INCLUDE): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($needle, $message) {
            if (!str_contains($input, $needle)) {
                throw new SanitizrValidationException(sprintf($message, $needle));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to match the given regular expression pattern.
     *
     * @param string $pattern The regular expression pattern to match.
     * @param string $message The error message to use if validation fails.
     * @return static The current schema instance for method chaining.
     */
    public function regex(string $pattern, string $message = SanitizrErrorMessages::STRING_NOT_MATCHING_REGEX): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($pattern, $message) {
            if (! preg_match($pattern, $input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule to ensure the string is a valid email address.
     *
     * Throws a SanitizrValidationException with the provided message if the string does not match a valid email format.
     *
     * @param string $message The error message to use if validation fails.
     * @return static
     */
    public function email(string $message = SanitizrErrorMessages::STRING_NOT_EMAIL): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to be a valid URL.
     *
     * Throws a SanitizrValidationException with the provided message if the string is not a valid URL.
     *
     * @param string $message The error message to use if validation fails.
     * @return static
     */
    public function url(string $message = SanitizrErrorMessages::STRING_NOT_URL): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_URL)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to start with the specified prefix.
     *
     * @param string $prefix The required starting substring.
     * @param string $message Optional custom error message.
     * @return static
     */
    public function startsWith(string $prefix, string $message = SanitizrErrorMessages::STRING_MUST_START_WITH): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($prefix, $message) {
            if (str_starts_with($input, $prefix) === false) {
                throw new SanitizrValidationException(sprintf($message, $prefix));
            }
        });

        return $newSchema;
    }

    /**
     * Adds a validation rule that requires the string to end with the specified suffix.
     *
     * Throws a SanitizrValidationException with a formatted message if the string does not end with the given suffix.
     *
     * @param string $suffix The required ending substring.
     * @param string $message The error message to use if validation fails.
     * @return static
     */
    public function endsWith(string $suffix, string $message = SanitizrErrorMessages::STRING_MUST_END_WITH): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input) use ($suffix, $message) {
            if (str_ends_with($input, $suffix) === false) {
                throw new SanitizrValidationException(sprintf($message, $suffix));
            }
        });

        return $newSchema;
    }

    public function transform(callable $transformer): static
    {
        $newSchema = clone $this;
        $newSchema->addTransform($transformer);

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
     * @param string $message The error message template, with `%s` replaced by the path or 'Value'.
     * @param string $path The path or field name for error reporting.
     * @return string The validated string input.
     * @throws SanitizrValidationException If the input is not a string.
     */
    protected function parseValue(mixed $input, string $message = SanitizrErrorMessage::VALUE_MUST_BE_STRING, string $path = ''): string
    {
        if (! is_string($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        return $input;
    }
}
