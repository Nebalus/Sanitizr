<?php

namespace Nebalus\Sanitizr\Trait;

trait SchemaStringableTrait
{
    private bool $isStringable = false;

    /**
     * Determines whether the current Schemainstance is can accept other data types
     *
     * @return bool True if the instance can accept other datatypes than its own; otherwise, false.
     */
    public function isStringable(): bool
    {
        return $this->isStringable;
    }

    /**
     * Marks the current Schemainstance is able to accept other data types and trys to convert in into the schemasy desired datatype
     *
     * @return static and it will accept other datatypes when the schema is parsed
     */
    public function stringable(): static
    {
        $this->isStringable = true;
        return $this;
    }
}
