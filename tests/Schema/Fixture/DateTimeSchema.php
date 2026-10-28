<?php

declare(strict_types=1);

namespace UnitTesting\Schema\Fixture;

use DateTimeImmutable;
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

/**
 * Accepts DateTimeImmutable instances only; the decoded side of a date codec.
 */
final class DateTimeSchema extends AbstractSanitizrSchema
{
    protected function parseValue(mixed $input, string $path = ''): DateTimeImmutable
    {
        if (! $input instanceof DateTimeImmutable) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be a DateTimeImmutable",
                expected: 'DateTimeImmutable',
                received: get_debug_type($input),
            ));
        }

        return $input;
    }
}
