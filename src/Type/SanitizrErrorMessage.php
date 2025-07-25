<?php

namespace Nebalus\Sanitizr\Type;

final class SanitizrErrorMessage
{
    public const string STRING_LENGTH = "Must be exact %s characters long";
    public const string STRING_MIN_LENGTH = "Must be %s or more characters long";
    public const string STRING_MAX_LENGTH = "Must be %s or fewer characters long";
    public const string STRING_BETWEEN_RANGE = "Must be between %s and %s";
    public const string STRING_ONLY_UPPERCASE = "Must be uppercase";
    public const string STRING_ONLY_LOWERCASE = "Must be lowercase";
    public const string STRING_MUST_INCLUDE = 'Must include "%s"';
    public const string STRING_NOT_MATCHING_REGEX = "Does not match the pattern";
    public const string STRING_NOT_EMAIL = "Not a valid E-Mail address";
    public const string STRING_NOT_URL = "Not a valid URL";
    public const string STRING_MUST_START_WITH = "Does not start with required string prefix";
    public const string STRING_MUST_END_WITH = "Does not end with required string suffix";
}
