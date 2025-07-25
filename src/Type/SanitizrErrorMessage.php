<?php

namespace Nebalus\Sanitizr\Type;

final class SanitizrErrorMessage
{
    public const string VALUE_MUST_BE_STRING = "%s must be a STRING";
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
    public const string VALUE_MUST_BE_NUMERIC = "%s must be NUMERIC";
    public const string NUMBER_MUST_BE_GREATER_THAN = "Must be greater than %s";
    public const string NUMBER_MUST_BE_GREATER_THAN_OR_EQUAL = "Must be greater than or equal to %s";
    public const string NUMBER_MUST_BE_LESS_THAN = "Must be less than %s";
    public const string NUMBER_MUST_BE_LESS_THAN_OR_EQUAL = "Must be less than or equal to %s";
    public const string NUMBER_MUST_BE_FLOAT = "Must be a float number";
    public const string NUMBER_MUST_BE_INTEGER = "Must be an integer number";
    public const string NUMBER_MUST_BE_POSITIVE = "Must be a positive number";
    public const string NUMBER_MUST_BE_NONPOSITIVE = "Must be a negative number or 0";
    public const string NUMBER_MUST_BE_NEGATIVE = "Must be a negative number";
    public const string NUMBER_MUST_BE_NONNEGATIVE = "Must be a positive number or 0";
    public const string NUMBER_MUST_BE_MULTIPLE_OF = "Must be a multiple of %s";
    public const string VALUE_MUST_BE_NULL = "%s must be NULL";
    public const string VALUE_MUST_BE_BOOLEAN = "%s must be an BOOLEAN";
}
