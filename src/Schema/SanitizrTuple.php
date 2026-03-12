<?php

namespace Nebalus\Sanitizr\Schema;

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
    protected function parseValue(mixed $input, string $message = "%s must be an ARRAY", string $path = ''): array
    {
        if (! is_array($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        $result = [];

        $iterator = 0;
        foreach ($this->schemas as $schema) {
            $updatedPath = $path === '' ? "KEY" . $iterator : $path . ".KEY" . $iterator;

            if ($schema->isOptional() === false && isset($input[$iterator]) === false && $schema->isNullable() === false) {
                throw new SanitizrValidationException("The value at position " . $updatedPath . " is required");
            }
            $currentValue = $input[$iterator] ?? null;
            $result[] = $schema->parse(
                $currentValue,
                path: $updatedPath
            );

            $iterator++;
        }

        return $result;
    }
}
