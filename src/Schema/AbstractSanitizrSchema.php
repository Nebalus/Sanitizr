<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Value\SafeParsedData;

abstract class AbstractSanitizrSchema
{
    private array $transformQueue = [];
    private array $checkQueue = [];

    private bool $isOptional = false;
    private bool $isNullable = false;

    private bool $hasDefaultValue = false;
    private mixed $defaultValue;

    private ?array $orSchemas = [];
    private ?array $andSchemas = [];

    /**
     * Adds a transformation callable to the transformation queue for sequential application during parsing.
     *
     * @param callable $transform The transformation function to be applied to the parsed value.
     */
    protected function addTransform(callable $transform): void
    {
        $this->transformQueue[] = [$transform];
    }

    /**
     * Adds a validation callable to the check queue for later execution during parsing.
     *
     * The callable will be invoked to validate the parsed value.
     */
    protected function addCheck(callable $callable): void
    {
        $this->checkQueue[] = [$callable];
    }

    /**
     * Marks the schema as optional, allowing validation to succeed if the field is missing.
     *
     * This setting is only relevant when the schema is used as a property within an object schema.
     *
     * @return static
     */
    public function optional(): static
    {
        $newSchema = clone $this;
        $newSchema->isOptional = true;
        return $newSchema;
    }

    /**
     * Marks the schema as non-optional, requiring the field to be present during validation.
     *
     * This setting is only relevant when used within an object schema.
     *
     * @return static
     */
    public function nonOptional(): static
    {
        $newSchema = clone $this;
        $newSchema->isOptional = false;
        return $newSchema;
    }

    /**
     * Marks the schema as allowing null values.
     *
     * @return static The schema instance with nullability enabled.
     */
    public function nullable(): static
    {
        $newSchema = clone $this;
        $newSchema->isNullable = true;
        return $newSchema;
    }

    /**
     * Marks the schema as both nullable and optional.
     *
     * The schema will accept missing or null values without failing validation.
     *
     * @return static
     */
    public function nullish(): static
    {
        $newSchema = clone $this;
        $newSchema->isNullable = true;
        $newSchema->isOptional = true;
        return $newSchema;
    }

    /**
     * Sets the default value, if the value is null or not defined in an object schema
     * @param mixed $value
     * @return static
     */
    public function default(mixed $value): static
    {
        $newSchema = clone $this;
        $newSchema->hasDefaultValue = true;
        $newSchema->defaultValue = $value;
        return $newSchema;
    }

    public function or(AbstractSanitizrSchema $orSanitizerSchema): static
    {
        $newSchema = clone $this;
        $newSchema->orSchemas[] = $orSanitizerSchema;
        return $newSchema;
    }

    public function and(AbstractSanitizrSchema $andSanitizerSchema): static
    {
        $newSchema = clone $this;
        $newSchema->andSchemas[] = $andSanitizerSchema;
        return $newSchema;
    }

    /**
     * Returns whether the schema is marked as optional.
     *
     * This is relevant when the schema is used as a property within an object schema, indicating that the field may be omitted without causing validation to fail.
     *
     * @return bool True if the schema is optional, false otherwise.
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * Determines whether the schema allows null values.
     *
     * @return bool True if the schema is nullable; otherwise, false.
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * Determines if the schema is both nullable and optional.
     *
     * @return bool True if the schema allows null values and is optional.
     */
    public function isNullish()
    {
        return $this->isNullable && $this->isOptional();
    }

    /**
     * Determines whether a default value is set for the schema.
     *
     * @return bool True if a default value is defined; otherwise, false.
     */
    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * Returns the default value set for the schema, if any.
     *
     * @return mixed The default value, or null if no default is set.
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * Parses and validates the input value according to the schema, applying transformations and checks.
     *
     * If the schema is nullable and the input is null, returns null. If a default value is set and the input is null, returns the default value. Otherwise, parses the input, applies all registered transformations and validation checks, and enforces all "and" schemas. If validation fails and "or" schemas are defined, attempts to parse the input with each "or" schema until one succeeds. Throws a SanitizrValidationException if no schema validates the input.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $path The path to the value, used for error reporting.
     * @return mixed The parsed and validated value, or null/default if applicable.
     * @throws SanitizrValidationException If the input does not satisfy any schema.
     */
    public function parse(mixed $input, string $path = ''): mixed
    {
        if ($this->isNullable && is_null($input)) {
            return null;
        }

        if (is_null($input) && $this->hasDefaultValue()) {
            return $this->defaultValue;
        }

        try {
            $parsedValue = $this->parseValue($input, path: $path);

            foreach ($this->transformQueue as $value) {
                $parsedValue = $value[0]($parsedValue);
            }

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
