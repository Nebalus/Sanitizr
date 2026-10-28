<?php

namespace Nebalus\Sanitizr\Schema\Union;

use InvalidArgumentException;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

class SanitizrUnion extends AbstractSanitizrSchema
{
    /** @var AbstractSanitizrSchema[] */
    private array $schemas;

    /**
     * Initializes a union schema.
     *
     * @param AbstractSanitizrSchema ...$schemas The schemas to include in the union.
     * @throws InvalidArgumentException If no schemas are provided.
     */
    public function __construct(AbstractSanitizrSchema ...$schemas)
    {
        if (count($schemas) === 0) {
            throw new InvalidArgumentException("Union must have at least one schema option.");
        }

        $this->schemas = $schemas;
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $path = ''): mixed
    {
        return $this->walk($input, $path, encode: false);
    }

    /**
     * Encodes the value with the first schema that accepts it; see {@see parseValue()}.
     *
     * @throws SanitizrValidationException
     */
    protected function encodeValue(mixed $input, string $path = ''): mixed
    {
        return $this->walk($input, $path, encode: true);
    }

    /**
     * @throws SanitizrValidationException
     */
    private function walk(mixed $input, string $path, bool $encode): mixed
    {
        foreach ($this->schemas as $schema) {
            try {
                return self::runChild($schema, $input, $path, $encode);
            } catch (SanitizrValidationException) {
                // If it fails, we simply continue to the next schema.
                continue;
            }
        }

        throw SanitizrValidationException::fromIssue(new SanitizrIssue(
            code: SanitizrIssue::INVALID_UNION,
            path: self::pathToArray($path),
            message: "Invalid input. Did not match any of the allowed schemas.",
            expected: 'union',
            received: gettype($input) === 'object' ? 'object' : gettype($input),
        ));
    }
}
