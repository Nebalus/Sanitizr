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
    protected function parseValue(mixed $input, string $message = '%s must be an OBJECT or an ASSOCIATIVE ARRAY', string $path = ''): array
    {
        if (is_object($input)) {
            $input = get_object_vars($input);
        }

        if (!is_array($input)) {
            throw new SanitizrValidationException(sprintf($message, $path !== '' ? $path : 'Value'));
        }

        $result = [];

        foreach ($this->schemas as $prop => $schema) {
            if ($schema instanceof AbstractSanitizrSchema) {
                $updatedPath = $path === '' ? $prop : $path . "." . $prop;

                if ($schema->hasDefaultValue() && array_key_exists($prop, $input) === false) {
                    $result[$prop] = $schema->getDefaultValue();
                    continue;
                }

                if ($schema->isOptional() === false && array_key_exists($prop, $input) === false) {
                    throw new SanitizrValidationException($updatedPath . " is required");
                }

                if ($schema->isOptional() === true && array_key_exists($prop, $input) === false) {
                    if ($schema->isNullable() === true) {
                        $result[$prop] = null;
                        continue;
                    }
                    continue;
                }


                if ($schema instanceof SanitizrObjectSchema) {
                    if (array_key_exists($prop, $input)) {
                        $result[$prop] = $schema->parseValue(
                            $input[$prop],
                            path: $updatedPath
                        );
                    }
                    continue;
                }

                $result[$prop] = $schema->parse(
                    $input[$prop] ?? null,
                    path: $updatedPath
                );
            }
        }

        return $result;
    }
}
