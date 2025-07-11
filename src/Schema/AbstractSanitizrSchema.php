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

    private ?array $orSchemas = [];
    private ?array $andSchemas = [];

    protected function addCheck(callable $callable): void
    {
        $this->checkQueue[] = [$callable];
    }

    /**
     * Marks that the value can be optional
     * NOTE: This is only used in an object schema
     * @param string $message
     * @return static
     */
    public function optional(): static
    {
        $this->isOptional = true;
        return $this;
    }

    /**
     * Marks that the value can be null
     * @param string $message
     * @return static
     */
    public function nullable(): static
    {
        $this->isNullable = true;
        return $this;
    }

    /**
     * Sets the default value, if the value is null or not defined when its optional in an object schema
     * @param mixed $value
     * @return static
     */
    public function default(mixed $value): static
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function or(AbstractSanitizrSchema $orSanitizerSchema): static
    {
        $this->orSchemas[] = $orSanitizerSchema;
        return $this;
    }

    public function and(AbstractSanitizrSchema $andSanitizerSchema): static
    {
        $this->andSchemas[] = $andSanitizerSchema;
        return $this;
    }

    /**
     * Is only used, if this schema is in an object schema
     * @return bool
     */
    protected function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * @return bool
     */
    protected function isNullable(): bool
    {
        return $this->isNullable;
    }

    protected function hasDefaultValue(): bool
    {
        return isset($this->defaultValue);
    }

    protected function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * Parse the input value
     * @throws SanitizrValidationException
     */
    public function parse(mixed $input, string $path = ''): mixed
    {
        if ($this->isNullable && is_null($input)) {
            return null;
        }

        if (is_null($input) && isset($this->defaultValue)) {
            return $this->defaultValue;
        }

        try {
            $parsedValue = $this->parseValue($input, path: $path);

            foreach ($this->checkQueue as $check) {
                $check[0]($parsedValue);
            }

            if ($this->andSchemas !== []) {
                foreach ($this->andSchemas as $andSchema) {
                    $andSchema->parse($parsedValue, path: $path);
                }
            }

            return $parsedValue;
        } catch (SanitizrValidationException $e) {
            if ($this->orSchemas === []) {
                throw $e;
            }

            foreach ($this->orSchemas as $orSchema) {
                try {
                    return $orSchema->parse($input, path: $path);
                } catch (SanitizrValidationException) {
                    // Ignore the exception and try the next schema
                }
            }
            throw new SanitizrValidationException("No valid schema found for the input value at path: " . $path, 0, $e);
        }
    }

    /**
     * Parses the input value and return a SafeParsedData object and will not throw an exception
     */
    public function safeParse(mixed $input, string $path = ''): SafeParsedData
    {
        try {
            $result = $this->parse($input, path: $path);
            return SafeParsedData::from(true, $result, null);
        } catch (SanitizrValidationException $e) {
            return SafeParsedData::from(false, null, $e->getMessage());
        }
    }

    /**
     * @throws SanitizrValidationException
     */
    abstract protected function parseValue(mixed $input, string $message, string $path = ''): mixed;
}
