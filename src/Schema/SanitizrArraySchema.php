<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrArraySchema extends AbstractSanitizrSchema
{
    public function __construct(
        private readonly AbstractSanitizrSchema $schema
    ) {
    }

    protected function parseValue(mixed $input): array
    {
        if (! is_array($input)) {
            throw new SanitizrValidationException('Not an array');
        }

        $result = [];

        foreach ($input as $v) {
            $result[] = $this->schema->parse($v);
        }

        return $result;
    }
}
