<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrNull extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizrValidationException
     */
    protected function parseValue(mixed $input, string $path = ''): null
    {
        if (! is_null($input)) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: sprintf("%s must be NULL", $path !== '' ? $path : 'Value'),
                expected: 'null',
                received: gettype($input),
            ));
        }

        return null;
    }
}
