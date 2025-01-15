<?php

namespace Nebalus\Sanitizr\Checks\Boundary;

use Nebalus\Sanitizr\Checks\AbstractCheck;

abstract class AbstractBoundaryCheck extends AbstractCheck
{
    public function check(mixed $value): bool
    {
        if (is_string($value)) {
            return $this->checkBoundary(strlen($value));
        }

        return $this->checkBoundary($value);
    }

    abstract protected function checkBoundary(int|float $value): bool;
}
