<?php

namespace Nebalus\Sanitizr\Schema;

use Closure;
use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Value\SafeParsedData;

abstract class AbstractSanitizrSchema
{
    private array $transformQueue = [];
    private array $checkQueue = [];
    private array $postTransformQueue = [];

    private bool $isOptional = false;
    private bool $isNullable = false;

    private bool $hasDefaultValue = false;
    private mixed $defaultValue;

    private bool $hasCatchValue = false;
    private mixed $catchValue;

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

    /**
     * Sets a fallback value that is returned instead of failing when the input does not satisfy the schema.
     *
     * Pass a Closure to compute the fallback; it is called with the validation error and the original input:
     * `fn(SanitizrError $error, mixed $input): mixed`. Any other value, including a callable string, is
     * returned as it is. Within an object schema, a missing key is caught as well.
     *
     * @param mixed $value The fallback value, or a Closure producing it.
     * @return static
     */
    public function catch(mixed $value): static
    {
        $newSchema = clone $this;
        $newSchema->hasCatchValue = true;
        $newSchema->catchValue = $value;
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
     * Maps the parsed value using the given transformation function.
     * The transform is executed after all validation checks have passed.
     *
     * @param callable $transformer The function transforming the parsed data.
     * @return static
     */
    public function transform(callable $transformer): static
    {
        $newSchema = clone $this;
        $newSchema->postTransformQueue[] = [$transformer];
        return $newSchema;
    }

    /**
     * Pipes the output of this schema into another schema.
     *
     * The input is parsed with this schema first; its result, after any transforms, is then parsed with
     * the given schema. Useful to validate a value again once it has been transformed into another type.
     *
     * @param AbstractSanitizrSchema $schema The schema that parses the output of this schema.
     * @return SanitizrPipe The pipe schema.
     */
    public function pipe(AbstractSanitizrSchema $schema): SanitizrPipe
    {
        return new SanitizrPipe($this, $schema);
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
     * Determines whether a catch value is set for the schema.
     *
     * @return bool True if a catch value is defined; otherwise, false.
     */
    public function hasCatchValue(): bool
    {
        return $this->hasCatchValue;
    }

    /**
     * Resolves the catch value for a failed input, calling it first if it is a Closure.
     *
     * @param SanitizrError $error The error the input failed with.
     * @param mixed $input The input that failed.
     * @return mixed The fallback value.
     */
    protected function resolveCatchValue(SanitizrError $error, mixed $input): mixed
    {
        return $this->catchValue instanceof Closure
            ? ($this->catchValue)($error, $input)
            : $this->catchValue;
    }

    /**
     * Answers a required key that is missing from an object or tuple: the schema's catch value, if it has one.
     *
     * @param AbstractSanitizrSchema $schema The schema of the missing key.
     * @param string $path The path to the missing key.
     * @return mixed The catch value of the schema.
     * @throws SanitizrValidationException With a missing_key issue if the schema has no catch value.
     */
    protected static function parseMissingKey(AbstractSanitizrSchema $schema, string $path): mixed
    {
        $issue = new SanitizrIssue(
            code: SanitizrIssue::MISSING_KEY,
            path: self::pathToArray($path),
            message: "Required",
            expected: 'defined',
            received: 'undefined',
        );

        if ($schema->hasCatchValue() === false) {
            throw SanitizrValidationException::fromIssue($issue);
        }

        return $schema->resolveCatchValue(new SanitizrError($issue), null);
    }

    /**
     * Converts a dot-separated path string to a path array.
     *
     * @param string $path Dot-separated path (e.g. "user.address.zip")
     * @return array Path segments (e.g. ["user", "address", "zip"])
     */
    protected static function pathToArray(string $path): array
    {
        return $path !== '' ? explode('.', $path) : [];
    }

    /**
     * Parses and validates the input value according to the schema, applying transformations and checks.
     *
     * If the schema is nullable and the input is null, returns null.
     * If a default value is set and the input is null, returns the default value.
     * Otherwise, parses the input, applies all registered transformations and validation checks,
     * and enforces all "and" schemas. If validation fails and "or" schemas are defined,
     * attempts to parse the input with each "or" schema until one succeeds.
     * Throws a SanitizrValidationException if no schema validates the input,
     * unless a catch value is set, in which case that value is returned instead.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $path The path to the value, used for error reporting.
     * @return mixed The parsed and validated value, or null/default/catch value if applicable.
     * @throws SanitizrValidationException If the input does not satisfy any schema.
     */
    public function parse(mixed $input, string $path = ''): mixed
    {
        if ($this->hasCatchValue === false) {
            return $this->parseOrThrow($input, $path);
        }

        try {
            return $this->parseOrThrow($input, $path);
        } catch (SanitizrValidationException $e) {
            return $this->resolveCatchValue($e->getError(), $input);
        }
    }

    /**
     * @throws SanitizrValidationException
     */
    private function parseOrThrow(mixed $input, string $path): mixed
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
                $check[0]($parsedValue, $path);
            }

            if ($this->andSchemas !== []) {
                foreach ($this->andSchemas as $andSchema) {
                    $andSchema->parse($parsedValue, path: $path);
                }
            }

            foreach ($this->postTransformQueue as $value) {
                $parsedValue = $value[0]($parsedValue);
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

            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_UNION,
                path: self::pathToArray($path),
                message: "No valid schema found for the input value",
            ));
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
            return SafeParsedData::from(false, null, $e->getError());
        }
    }

    /**
     * @throws SanitizrValidationException
     */
    abstract protected function parseValue(mixed $input, string $path = ''): mixed;
}
