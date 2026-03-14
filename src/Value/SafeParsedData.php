<?php

namespace Nebalus\Sanitizr\Value;

use Nebalus\Sanitizr\Error\SanitizrError;

readonly class SafeParsedData
{
    private function __construct(
        private bool $valid,
        private mixed $value,
        private SanitizrError|null $error
    ) {
    }

    public static function from(bool $valid, mixed $value, SanitizrError|null $error): self
    {
        return new self($valid, $value, $error);
    }

    /**
     * @deprecated [v1.0.1] [Use isValid() instead]
     */
    public function isSuccess(): bool
    {
        return $this->isValid();
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->valid === false;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Returns the structured error object, or null if validation succeeded.
     */
    public function getError(): ?SanitizrError
    {
        return $this->error;
    }

    /**
     * Returns a flat error message string, or null if validation succeeded.
     *
     * @deprecated [v2.0.0] [Use getError()->getMessage() instead]
     */
    public function getErrorMessage(): ?string
    {
        return $this->error?->getMessage();
    }
}
