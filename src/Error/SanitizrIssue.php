<?php

namespace Nebalus\Sanitizr\Error;

readonly class SanitizrIssue
{
    public const string INVALID_TYPE = 'invalid_type';
    public const string TOO_SMALL = 'too_small';
    public const string TOO_BIG = 'too_big';
    public const string INVALID_STRING = 'invalid_string';
    public const string INVALID_LITERAL = 'invalid_literal';
    public const string INVALID_ENUM_VALUE = 'invalid_enum_value';
    public const string MISSING_KEY = 'missing_key';
    public const string INVALID_UNION = 'invalid_union';
    public const string INVALID_UNION_DISCRIMINATOR = 'invalid_union_discriminator';
    public const string NOT_MULTIPLE_OF = 'not_multiple_of';
    public const string CUSTOM = 'custom';

    /**
     * @param string $code     Issue code — use the SanitizrIssue::* constants
     * @param array  $path     Breadcrumb path to the invalid field (e.g. ["user", "address", "zip"])
     * @param string $message  Human-readable error message
     * @param string|null $expected  Expected type or constraint (e.g. "string", "min:3")
     * @param string|null $received  Received type or value description (e.g. "integer", "null")
     */
    public function __construct(
        public string $code,
        public array $path,
        public string $message,
        public ?string $expected = null,
        public ?string $received = null,
    ) {
    }

    /**
     * Returns the dot-separated path string (e.g. "user.address.zip")
     */
    public function getPathString(): string
    {
        return implode('.', $this->path);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'code' => $this->code,
            'path' => $this->path,
            'message' => $this->message,
        ];

        if ($this->expected !== null) {
            $result['expected'] = $this->expected;
        }

        if ($this->received !== null) {
            $result['received'] = $this->received;
        }

        return $result;
    }
}
