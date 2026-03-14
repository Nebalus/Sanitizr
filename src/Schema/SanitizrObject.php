<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrObject extends AbstractSanitizrSchema
{
    /**
     * Initializes the SanitizrObject with a set of property schemas.
     *
     * @param array $schemas An associative array mapping property names to their corresponding schemas.
     */
    public function __construct(
        private readonly array $schemas
    ) {
    }

    /**
     * Retrieves the associative array of property schemas.
     *
     * @return array<string, AbstractSanitizrSchema> The property schemas defined for this object.
     */
    public function getPropertySchemas(): array
    {
        return $this->schemas;
    }

    /**
     * Parses and validates an input value as an object or associative array according to the defined schemas.
     *
     * Collects ALL validation errors across all properties before throwing, so the caller
     * receives every issue at once (Zod-style). Nested objects and child schemas also
     * contribute their issues to the same error collection.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $path The current property path for error reporting in nested structures.
     * @return array The parsed and validated associative array.
     * @throws SanitizrValidationException If the input is not an object/associative array or any properties fail validation.
     */
    protected function parseValue(mixed $input, string $path = ''): array
    {
        if (is_object($input)) {
            $input = get_object_vars($input);
        }

        if (!is_array($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be an OBJECT or an ASSOCIATIVE ARRAY",
                expected: 'object',
                received: gettype($input),
            ));
        }

        $result = [];
        $collectedErrors = new SanitizrError();

        foreach ($this->schemas as $prop => $schema) {
            if ($schema instanceof AbstractSanitizrSchema) {
                $updatedPath = $path === '' ? $prop : $path . "." . $prop;

                if ($schema->hasDefaultValue() && array_key_exists($prop, $input) === false) {
                    $result[$prop] = $schema->getDefaultValue();
                    continue;
                }

                if ($schema->isOptional() === false && array_key_exists($prop, $input) === false) {
                    $collectedErrors->addIssue(new SanitizrIssue(
                        code: SanitizrIssue::MISSING_KEY,
                        path: self::pathToArray($updatedPath),
                        message: $updatedPath . " is required",
                        expected: 'defined',
                        received: 'undefined',
                    ));
                    continue;
                }

                if ($schema->isOptional() === true && array_key_exists($prop, $input) === false) {
                    continue;
                }

                try {
                    if ($schema instanceof SanitizrObject) {
                        if (array_key_exists($prop, $input)) {
                            $result[$prop] = $schema->parseValue(
                                $input[$prop],
                                path: $updatedPath
                            );
                        }
                    } else {
                        $result[$prop] = $schema->parse(
                            $input[$prop] ?? null,
                            path: $updatedPath
                        );
                    }
                } catch (SanitizrValidationException $e) {
                    $collectedErrors->merge($e->getError());
                }
            }
        }

        if ($collectedErrors->hasIssues()) {
            throw SanitizrValidationException::fromError($collectedErrors);
        }

        return $result;
    }
}
