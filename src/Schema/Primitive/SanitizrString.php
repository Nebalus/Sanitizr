<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use InvalidArgumentException;
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


    public function uuid(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid UUID",
                ));
            }
        });
        return $newSchema;
    }

    public function httpUrl(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_URL) || ! preg_match('/^https?:\/\//i', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid HTTP(S) URL",
                ));
            }
        });
        return $newSchema;
    }

    public function hostname(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid hostname",
                ));
            }
        });
        return $newSchema;
    }

    public function emoji(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F000}-\x{1F02F}\x{1F0A0}-\x{1F0FF}\x{1F1E6}-\x{1F1FF}\x{1F200}-\x{1F2FF}\x{1F900}-\x{1F9FF}]+$/u', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid emoji",
                ));
            }
        });
        return $newSchema;
    }

    public function base64(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid base64",
                ));
            }
        });
        return $newSchema;
    }

    public function base64url(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^(?:[A-Za-z0-9_-]{4})*(?:[A-Za-z0-9_-]{2}|[A-Za-z0-9_-]{3})?$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid base64url",
                ));
            }
        });
        return $newSchema;
    }

    public function hex(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[a-fA-F0-9]+$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid hex string",
                ));
            }
        });
        return $newSchema;
    }

    public function jwt(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid JWT",
                ));
            }
        });
        return $newSchema;
    }

    public function nanoid(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[a-zA-Z0-9_-]{21}$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid nanoid",
                ));
            }
        });
        return $newSchema;
    }

    public function cuid(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^c[^\s-]{8,}$/i', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid CUID",
                ));
            }
        });
        return $newSchema;
    }

    public function cuid2(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[a-z][a-z0-9]*$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid CUID2",
                ));
            }
        });
        return $newSchema;
    }

    public function ulid(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^[0-9A-HJKMNP-TV-Z]{26}$/i', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid ULID",
                ));
            }
        });
        return $newSchema;
    }

    public function ipv4(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid IPv4",
                ));
            }
        });
        return $newSchema;
    }

    public function ipv6(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid IPv6",
                ));
            }
        });
        return $newSchema;
    }

    public function mac(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_MAC)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid MAC address",
                ));
            }
        });
        return $newSchema;
    }

    public function cidrv4(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}\/([0-9]|[1-2][0-9]|3[0-2])$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid IPv4 CIDR",
                ));
            }
            $parts = explode('/', $input);
            if (! filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && ! filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid IPv4 CIDR",
                ));
            }
        });
        return $newSchema;
    }

    public function cidrv6(?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($message) {
            if (! preg_match('/^([0-9a-fA-F:\.]+)\/([0-9]|[1-9][0-9]|1[0-1][0-9]|12[0-8])$/', $input, $matches)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid IPv6 CIDR",
                ));
            }
            if (! filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid IPv6 CIDR",
                ));
            }
        });
        return $newSchema;
    }

    public function hash(string $type, ?string $message = null): static
    {
        $newSchema = clone $this;
        $newSchema->addCheck(function (string $input, string $path) use ($type, $message) {
            $lengths = [
                'md5' => 32,
                'sha1' => 40,
                'sha256' => 64,
                'sha384' => 96,
                'sha512' => 128
            ];

            if (! isset($lengths[$type])) {
                throw new InvalidArgumentException("Invalid hash type specified: $type");
            }

            $length = $lengths[$type];
            if (! preg_match('/^[a-fA-F0-9]{' . $length . '}$/', $input)) {
                throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                    code: SanitizrIssue::INVALID_STRING,
                    path: self::pathToArray($path),
                    message: $message ?? "Invalid $type hash",
                ));
            }
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
