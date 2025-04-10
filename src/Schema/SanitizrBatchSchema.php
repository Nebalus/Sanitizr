<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrBatchSchema extends AbstractSanitizrSchema
{
    private array $schemas;
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
            $updatedPath = $path === '' ? $iterator : $path . "." . $iterator;

            if ($schema->isOptional() === false && isset($input[$iterator]) === false && is_null($input[$iterator]) === false) {
                throw new SanitizrValidationException("The value a position " . $updatedPath . " is required");
            }
            $currentValue = $input[$iterator] ?? null;
            $schema->parse($currentValue);
            $result[] = $schema->parse($currentValue);
        }

        return $result;
    }
}
