<?php

namespace Nebalus\Sanitizr\Schema;

use InvalidArgumentException;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrDiscriminatedUnion extends AbstractSanitizrSchema
{
    /** @var array<string, SanitizrObject> */
    private array $schemaMap = [];

    /**
     * Initializes a discriminated union schema.
     *
     * @param string $discriminator The key in the input array/object to use for discriminating the schema.
     * @param SanitizrObject ...$schemas The schemas to include in the union. Each must have a SanitizrLiteral schema at the discriminator key.
     * @throws InvalidArgumentException If a schema does not have a literal value at the discriminator key.
     */
    public function __construct(
        private readonly string $discriminator,
        SanitizrObject ...$schemas
    ) {
        if (count($schemas) === 0) {
            throw new InvalidArgumentException("Discriminated union must have at least one schema option.");
        }

        foreach ($schemas as $schema) {
            $properties = $schema->getPropertySchemas();

            if (!isset($properties[$this->discriminator])) {
                throw new InvalidArgumentException("Schema in discriminated union does not have the discriminator key '{$this->discriminator}' defined.");
            }

            $discriminatorSchema = $properties[$this->discriminator];

            if (!$discriminatorSchema instanceof SanitizrLiteral) {
                throw new InvalidArgumentException("Discriminator key '{$this->discriminator}' must be a SanitizrLiteral schema.");
            }

            $literalValue = $discriminatorSchema->getLiteralValue();

            if (is_array($literalValue)) {
                foreach ($literalValue as $val) {
                    $key = (string)$val;
                    if (isset($this->schemaMap[$key])) {
                        throw new InvalidArgumentException("Duplicate discriminator value '{$key}' in discriminated union.");
                    }
                    $this->schemaMap[$key] = $schema;
                }
                continue;
            }
            $key = (string)$literalValue;
            if (isset($this->schemaMap[$key])) {
                throw new InvalidArgumentException("Duplicate discriminator value '{$key}' in discriminated union.");
            }
            $this->schemaMap[$key] = $schema;
        }
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $path = ''): mixed
    {
        if (is_object($input)) {
            $input = get_object_vars($input);
        }

        if (!is_array($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be an OBJECT or an ASSOCIATIVE ARRAY",
                expected: 'object',
                received: gettype($input),
            ));
        }

        $discriminatorPath = $path !== '' ? $path . '.' . $this->discriminator : $this->discriminator;

        if (!isset($input[$this->discriminator])) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::MISSING_KEY,
                path: self::pathToArray($discriminatorPath),
                message: "Missing discriminator key '{$this->discriminator}'",
                expected: 'defined',
                received: 'undefined',
            ));
        }

        $discriminatorValue = (string) $input[$this->discriminator];

        if (!isset($this->schemaMap[$discriminatorValue])) {
            $expectedKeys = implode(', ', array_keys($this->schemaMap));
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_UNION_DISCRIMINATOR,
                path: self::pathToArray($discriminatorPath),
                message: "Invalid discriminator value '{$discriminatorValue}'. Expected one of: [{$expectedKeys}]",
                expected: $expectedKeys,
                received: $discriminatorValue,
            ));
        }

        // Delegate parsing to the matched schema — path propagates through the object schema
        $matchedSchema = $this->schemaMap[$discriminatorValue];
        return $matchedSchema->parse($input, path: $path);
    }
}
