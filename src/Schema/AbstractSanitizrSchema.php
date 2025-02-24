<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Value\SafeParsedData;

abstract class AbstractSanitizrSchema
{
    private array $checkQueue = [];

    private bool $isOptional = false;
    private bool $isNullable = false;
    private mixed $defaultValue;

    protected function addCheck(callable $callable): void
    {
        $this->checkQueue[] = [$callable];
    }

    public function optional(): static
    {
        $this->isOptional = true;
        return $this;
    }

    public function nullable(): static
    {
        $this->isNullable = true;
        return $this;
    }

    public function default(mixed $value): static
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * Is only used, if this schema is in an object schema
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * @throws SanitizrValidationException
     */
    public function parse(mixed $input): mixed
    {
        if ($this->isNullable && is_null($input)) {
            return null;
        }

        if (is_null($input) && isset($this->defaultValue)) {
            return $this->defaultValue;
        }

        $parsedValue = $this->parseValue($input);

        foreach ($this->checkQueue as $check) {
            $check[0]($parsedValue);
        }

        return $parsedValue;
    }

    public function safeParse(mixed $input): SafeParsedData
    {
        try {
            $result = $this->parse($input);
            return SafeParsedData::from(true, $result, null);
        } catch (SanitizrValidationException $e) {
            return SafeParsedData::from(false, null, $e->getMessage());
        }
    }

    /**
     * @throws SanitizrValidationException
     */
    abstract protected function parseValue(mixed $input): mixed;
}
