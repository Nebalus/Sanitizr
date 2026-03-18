<?php

namespace Nebalus\Sanitizr\Schema\Union;

use InvalidArgumentException;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use function Nebalus\Sanitizr\Schema\gettype;

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
        foreach ($this->schemas as $schema) {
            try {
                return $schema->parse($input, path: $path);
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
