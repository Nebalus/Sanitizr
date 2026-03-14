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
        if (! is_array($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: sprintf("%s must be an ARRAY", $path !== '' ? $path : 'Value'),
                expected: 'array',
                received: gettype($input),
            ));
        }

        $result = [];
        $collectedErrors = new SanitizrError();

        $iterator = 0;
        foreach ($this->schemas as $schema) {
            $updatedPath = $path === '' ? "KEY" . $iterator : $path . ".KEY" . $iterator;

            if ($schema->isOptional() === false && isset($input[$iterator]) === false && $schema->isNullable() === false) {
                $collectedErrors->addIssue(new SanitizrIssue(
                    code: SanitizrIssue::MISSING_KEY,
                    path: self::pathToArray($updatedPath),
                    message: "The value at position " . $updatedPath . " is required",
                    expected: 'defined',
                    received: 'undefined',
                ));
                $iterator++;
                continue;
            }

            try {
                $currentValue = $input[$iterator] ?? null;
                $result[] = $schema->parse(
                    $currentValue,
                    path: $updatedPath
                );
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
