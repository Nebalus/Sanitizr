<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrArraySchema extends AbstractSanitizrSchema
{
    public function __construct(
        private readonly AbstractSanitizrSchema $schema
    ) {
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = '%s must be an ARRAY', string $path = ''): array
    {
        if (! is_array($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        $result = [];

        foreach ($input as $v) {
            $result[] = $this->schema->parse($v);
        }

        return $result;
    }
}
