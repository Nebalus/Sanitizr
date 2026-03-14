<?php

namespace Nebalus\Sanitizr\Error;

class SanitizrError
{
    /** @var SanitizrIssue[] */
    private array $issues = [];

    public function __construct(SanitizrIssue ...$issues)
    {
        $this->issues = $issues;
    }

    /**
     * Add a single issue to this error collection.
     */
    public function addIssue(SanitizrIssue $issue): void
    {
        $this->issues[] = $issue;
    }

    /**
     * Merge all issues from another SanitizrError into this one.
     */
    public function merge(SanitizrError $other): void
    {
        foreach ($other->getIssues() as $issue) {
            $this->issues[] = $issue;
        }
    }

    /**
     * @return SanitizrIssue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }

    /**
     * Whether this error collection has any issues.
     */
    public function hasIssues(): bool
    {
        return $this->issues !== [];
    }

    /**
     * Returns a human-readable summary of all issues, one per line.
     */
    public function getMessage(): string
    {
        $lines = [];
        foreach ($this->issues as $issue) {
            $prefix = $issue->getPathString();
            $lines[] = ($prefix !== '' ? $prefix . ': ' : '') . $issue->message;
        }
        return implode("\n", $lines);
    }

    /**
     * Returns a serializable array representation of all issues.
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(fn(SanitizrIssue $issue) => $issue->toArray(), $this->issues);
    }
}
