<?php

namespace Nebalus\Sanitizr\Trait;

trait SchemaStringableTrait
{
    private bool $isStringable = false;

    public function isStringable(): bool
    {
        return $this->isStringable;
    }

    public function stringable(): static
    {
        $this->isStringable = true;
        return $this;
    }
}
