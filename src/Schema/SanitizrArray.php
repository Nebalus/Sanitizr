<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrArray extends AbstractSanitizrSchema
{
    /**
     * Initializes the SanitizrArray with a schema for validating each array element.
     *
     * @param AbstractSanitizrSchema $schema The schema used to validate and parse each element of the array.
     */
    public function __construct(
        private readonly AbstractSanitizrSchema $schema
    ) {
    }

    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $path = ''): array
    {
        if (! is_array($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be an ARRAY",
                expected: 'array',
                received: gettype($input),
            ));
        }

        $result = [];
        $collectedErrors = new SanitizrError();

        foreach ($input as $index => $v) {
            $updatedPath = $path === '' ? (string) $index : $path . '.' . $index;
            try {
                $result[] = $this->schema->parse($v, path: $updatedPath);
            } catch (SanitizrValidationException $e) {
                $collectedErrors->merge($e->getError());
            }
        }

        if ($collectedErrors->hasIssues()) {
            throw SanitizrValidationException::fromError($collectedErrors);
        }

        return $result;
    }
}
