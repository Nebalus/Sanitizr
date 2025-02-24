<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrObjectSchema extends AbstractSanitizrSchema
{
    public function __construct(
        private readonly array $schemas
    ) {
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $message = 'Not an object'): array
    {
        if (is_object($input)) {
            $input = get_object_vars($input);
        }

        if (!is_array($input)) {
            throw new SanitizrValidationException($message);
        }

        $result = [];

        foreach ($this->schemas as $prop => $schema) {
            if ($schema instanceof AbstractSanitizrSchema) {
                if ($schema->isOptional() === false && isset($input[$prop]) === false) {
                    throw new SanitizrValidationException($prop . " is required");
                }

                if ($schema instanceof SanitizrObjectSchema) {
                    if (isset($input[$prop]) && is_array($input[$prop])) {
                        $result[$prop] = $schema->parseValue($input[$prop]);
                    }
                    continue;
                }

                $result[$prop] = $schema->parse(
                    $input[$prop] ?? null
                );
            }
        }

        return $result;
    }
}
