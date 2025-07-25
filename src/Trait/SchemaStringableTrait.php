<?php

namespace Nebalus\Sanitizr\Trait;

trait SchemaStringableTrait
{
    private bool $isStringable = false;

    /**
     * Determines whether the current instance is marked as stringable.
     *
     * @return bool True if the instance is stringable; otherwise, false.
     */
    public function isStringable(): bool
    {
        return $this->isStringable;
    }

    /**
     * Marks the instance as stringable and enables method chaining.
     *
     * @return static The current instance with stringable state set to true.
     */
    public function stringable(): static
    {
        $this->isStringable = true;
        return $this;
    }
}
