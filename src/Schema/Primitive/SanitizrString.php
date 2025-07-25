<?php

namespace Nebalus\Sanitizr\Schema\Primitive;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SchemaStringableTrait;
use Nebalus\Sanitizr\Type\SanitizrErrorMessage;

class SanitizrString extends AbstractSanitizrSchema
{
    /**
     * @param int $length The string must be exact the provided length
     */
    public function length(int $length, string $message = SanitizrErrorMessage::STRING_LENGTH): static
    {
        $this->addCheck(function (string $input) use ($length, $message) {
            $inputLength = strlen($input);

            if ($inputLength !== $length) {
                throw new SanitizrValidationException(sprintf($message, $length));
            }
        });

        return $this;
    }

    public function min(int $min, string $message = SanitizrErrorMessage::STRING_MIN_LENGTH): static
    {
        $this->addCheck(function (string $input) use ($min, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min) {
                throw new SanitizrValidationException(sprintf($message, $min));
            }
        });

        return $this;
    }

    public function max(int $max, string $message = SanitizrErrorMessage::STRING_MAX_LENGTH): static
    {
        $this->addCheck(function (string $input) use ($max, $message) {
            $inputLength = strlen($input);

            if ($inputLength > $max) {
                throw new SanitizrValidationException(sprintf($message, $max));
            }
        });

        return $this;
    }

    public function between(int $min, int $max, string $message = SanitizrErrorMessage::STRING_BETWEEN_RANGE): static
    {
        $this->addCheck(function (string $input) use ($min, $max, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min || $inputLength > $max) {
                throw new SanitizrValidationException(sprintf($message, $min, $max));
            }
        });

        return $this;
    }

    public function uppercase(string $message = SanitizrErrorMessage::STRING_ONLY_UPPERCASE): static
    {
        $this->addCheck(function (string $input) use ($message) {
            if ($input !== strtoupper($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function lowercase(string $message = SanitizrErrorMessage::STRING_ONLY_LOWERCASE): static
    {
        $this->addCheck(function (string $input) use ($message) {
            if ($input !== strtolower($input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function includes(string $needle, string $message = SanitizrErrorMessage::STRING_MUST_INCLUDE): static
    {
        $this->addCheck(function (string $input) use ($needle, $message) {
            if (!str_contains($input, $needle)) {
                throw new SanitizrValidationException(sprintf($message, $needle));
            }
        });

        return $this;
    }

    public function regex(string $pattern, string $message = SanitizrErrorMessage::STRING_NOT_MATCHING_REGEX): static
    {
        $this->addCheck(function (string $input) use ($pattern, $message) {
            if (! preg_match($pattern, $input)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function email(string $message = SanitizrErrorMessage::STRING_NOT_EMAIL): static
    {
        $this->addCheck(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function url(string $message = SanitizrErrorMessage::STRING_NOT_URL): static
    {
        $this->addCheck(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_URL)) {
                throw new SanitizrValidationException($message);
            }
        });

        return $this;
    }

    public function startsWith(string $prefix, string $message = SanitizrErrorMessage::STRING_MUST_START_WITH): static
    {
        $this->addCheck(function (string $input) use ($prefix, $message) {
            if (str_starts_with($input, $prefix) === false) {
                throw new SanitizrValidationException(sprintf($message, $prefix));
            }
        });

        return $this;
    }

    public function endsWith(string $suffix, string $message = SanitizrErrorMessage::STRING_MUST_END_WITH): static
    {
        $this->addCheck(function (string $input) use ($suffix, $message) {
            if (str_ends_with($input, $suffix) === false) {
                throw new SanitizrValidationException(sprintf($message, $suffix));
            }
        });

        return $this;
    }

    public function transform(callable $transformer): static
    {
        $this->addTransform($transformer);

        return $this;
    }

    public function trim(): static
    {
        $this->addTransform(function (string $input): string {
            return trim($input);
        });

        return $this;
    }

    public function toLowerCase(): static
    {
        $this->addTransform(function (string $input): string {
            return strtolower($input);
        });

        return $this;
    }

    public function toUpperCase(): static
    {
        $this->addTransform(function (string $input): string {
            return strtoupper($input);
        });

        return $this;
    }

    public function toTitleCase(): static
    {
        $this->addTransform(function (string $input): string {
            return ucwords(strtolower($input));
        });

        return $this;
    }

    public function stripTags($allowedTags = null): static
    {
        $this->addTransform(function (string $input) use ($allowedTags): string {
            return strip_tags($input, $allowedTags);
        });

        return $this;
    }

    public function htmlSpecialChars(int $flags = ENT_QUOTES | ENT_SUBSTITUTE, ?string $encoding = null, bool $doubleEncode = true): static
    {
        $this->addTransform(function (string $input) use ($doubleEncode, $encoding, $flags): string {
            return htmlspecialchars($input, $flags, $encoding, $doubleEncode);
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
