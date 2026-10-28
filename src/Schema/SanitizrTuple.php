<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrTuple extends AbstractSanitizrSchema
{
    private array $schemas;
    /**
     * Initializes the tuple schema with one or more schema objects.
     *
     * A Tuple is a data structure used to describe an array with a fixed length
     * where each specific position (index) has a specific type.
     *
     * @param AbstractSanitizrSchema ...$schemas One or more schema instances to include mapped directly to their positional index.
     */
    public function __construct(
        AbstractSanitizrSchema ...$schemas
    ) {
        $this->schemas = $schemas;
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $path = ''): array
    {
        return $this->walk($input, $path, encode: false);
    }

    /**
     * Encodes every element with the schema at its position; see {@see parseValue()}.
     *
     * @throws SanitizrValidationException
     */
    protected function encodeValue(mixed $input, string $path = ''): array
    {
        return $this->walk($input, $path, encode: true);
    }

    /**
     * @throws SanitizrValidationException
     */
    private function walk(mixed $input, string $path, bool $encode): array
    {
        if (! is_array($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be an ARRAY",
                expected: 'array',
                received: gettype($input),
            ));
        }

        $result = [];
        $collectedErrors = new SanitizrError();

        $iterator = 0;
        foreach ($this->schemas as $schema) {
            $updatedPath = $path === '' ? "KEY" . $iterator : $path . ".KEY" . $iterator;

            $isMissing = $schema->isOptional() === false && isset($input[$iterator]) === false && $schema->isNullable() === false;

            try {
                $currentValue = $input[$iterator] ?? null;
                $result[] = $isMissing
                    ? self::parseMissingKey($schema, $updatedPath)
                    : self::runChild($schema, $currentValue, $updatedPath, $encode);
            } catch (SanitizrValidationException $e) {
                $collectedErrors->merge($e->getError());
            }

            $iterator++;
        }

        if ($collectedErrors->hasIssues()) {
            throw SanitizrValidationException::fromError($collectedErrors);
        }

        return $result;
    }
}
