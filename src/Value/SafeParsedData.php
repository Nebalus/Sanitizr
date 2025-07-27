<?php

namespace Nebalus\Sanitizr\Value;

readonly class SafeParsedData
{
    private function __construct(
        private bool $valid,
        private mixed $value,
        private string|null $error
    ) {
    }

    public static function from(bool $valid, mixed $value, string|null $error): self
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
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->error;
    }
}
