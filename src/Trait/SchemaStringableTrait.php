<?php

namespace Nebalus\Sanitizr\Trait;

trait SchemaStringableTrait
{
    private bool $isStringable = false;

    /**
     * Determines whether the current schema instance can accept other data types.
     *
     * @return bool True if the instance can accept other datatypes to be converted into its own; otherwise, false.
     */
    public function isStringable(): bool
    {
        return $this->isStringable;
    }

    /**
     * Marks the current schema instance as able to accept other data types, and tries to convert them into the schema's desired datatype.
     *
     * @return static Returns a new schema instance that accepts other datatypes.
     */
    public function stringable(): static
    {
        $newSchema = clone $this;
        $newSchema->isStringable = true;
        return $newSchema;
    }
}
