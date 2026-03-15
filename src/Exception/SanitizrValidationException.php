<?php

namespace Nebalus\Sanitizr\Exception;

use Exception;
use Nebalus\Sanitizr\Error\SanitizrError;
use Nebalus\Sanitizr\Error\SanitizrIssue;

class SanitizrValidationException extends Exception
{
    private SanitizrError $error;

    public function __construct(SanitizrError $error, int $code = 0, ?Exception $previous = null)
    {
        $this->error = $error;
        parent::__construct($error->getMessage(), $code, $previous);
    }

    /**
     * Creates an exception from a single SanitizrIssue.
     */
    public static function fromIssue(SanitizrIssue $issue): self
    {
        return new self(new SanitizrError($issue));
    }

    /**
     * Creates an exception from a SanitizrError collection.
     */
    public static function fromError(SanitizrError $error): self
    {
        return new self($error);
    }

    /**
     * Returns the structured error object containing all validation issues.
     */
    public function getError(): SanitizrError
    {
        return $this->error;
    }
}
