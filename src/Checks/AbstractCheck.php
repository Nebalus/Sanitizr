<?php

namespace Nebalus\Sanitizr\Checks;

abstract class AbstractCheck
{
    abstract public function check(mixed $value): bool;
}
