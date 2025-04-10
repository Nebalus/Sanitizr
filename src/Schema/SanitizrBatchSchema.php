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
            if (! isset($input[$iterator]) && ! is_null($input[$iterator])) {
                throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
            }
            $currentValue = $input[$iterator] ?? null;
            $schema->parse($currentValue);
            $result[] = $schema->parse($currentValue);
        }

        return $result;
    }
}
