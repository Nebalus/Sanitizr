<?php

namespace Nebalus\Sanitizr\Schema;

use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrPipe extends AbstractSanitizrSchema
{
    /**
     * Initializes a pipe that feeds the output of one schema into another.
     *
     * @param AbstractSanitizrSchema $in The schema the raw input is parsed with first.
     * @param AbstractSanitizrSchema $out The schema the output of the first schema is parsed with.
     */
    public function __construct(
        private readonly AbstractSanitizrSchema $in,
        private readonly AbstractSanitizrSchema $out
    ) {
    }

    /**
     * Retrieves the schema the raw input is parsed with first.
     */
    public function getInSchema(): AbstractSanitizrSchema
    {
        return $this->in;
    }

    /**
     * Retrieves the schema the output of the first schema is parsed with.
     */
    public function getOutSchema(): AbstractSanitizrSchema
    {
        return $this->out;
    }

    /**
     * Parses the input with the first schema, then parses its result with the second.
     *
     * @param mixed $input The value to be parsed and validated.
     * @param string $path The path to the value, used for error reporting.
     * @return mixed The value produced by the second schema.
     * @throws SanitizrValidationException If either schema rejects its input.
     */
    protected function parseValue(mixed $input, string $path = ''): mixed
    {
        return $this->out->parse($this->in->parse($input, path: $path), path: $path);
    }
}
