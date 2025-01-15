<?php

namespace Nebalus\Sanitizr\Value;

readonly class SafeParsedData
{
    private function __construct(
        private bool $success,
        private mixed $value,
        private string|null $error
    ) {
    }

    public static function from(bool $success, mixed $value, string|null $error): self
    {
        return new self($success, $value, $error);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->success === false;
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
