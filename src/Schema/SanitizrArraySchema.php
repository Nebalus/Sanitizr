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
    protected function parseValue(mixed $input, string $message = 'Value must be an ARRAY'): array
    {
        if (! is_array($input)) {
            throw new SanitizrValidationException($message);
        }

        $result = [];

        foreach ($input as $v) {
            $result[] = $this->schema->parse($v);
        }

        return $result;
    }
}
