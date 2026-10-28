<?php

namespace Nebalus\Sanitizr\Schema;

use Closure;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

class SanitizrCodec extends AbstractSanitizrSchema
{
    private readonly Closure $decoder;
    private readonly Closure $encoder;

    /**
     * Initializes a codec: a reversible conversion between the input type of one schema and the output type of another.
     *
     * @param AbstractSanitizrSchema $in The schema of the encoded form, e.g. an ISO date string.
     * @param AbstractSanitizrSchema $out The schema of the decoded form, e.g. a date object.
     * @param callable $decode Converts a value accepted by `$in` into one for `$out`.
     * @param callable $encode Converts a value accepted by `$out` back into one for `$in`.
     */
    public function __construct(
        private readonly AbstractSanitizrSchema $in,
        private readonly AbstractSanitizrSchema $out,
        callable $decode,
        callable $encode,
    ) {
        $this->decoder = $decode(...);
        $this->encoder = $encode(...);
    }

    /**
     * Retrieves the schema of the encoded form.
     */
    public function getInSchema(): AbstractSanitizrSchema
    {
        return $this->in;
    }

    /**
     * Retrieves the schema of the decoded form.
     */
    public function getOutSchema(): AbstractSanitizrSchema
    {
        return $this->out;
    }

    /**
     * Decodes the input: parses it with the input schema, converts it, and parses the result with the output schema.
     *
     * @param mixed $input The encoded value.
     * @param string $path The path to the value, used for error reporting.
     * @return mixed The decoded value.
     * @throws SanitizrValidationException If either schema rejects its value.
     */
    protected function parseValue(mixed $input, string $path = ''): mixed
    {
        $decoded = ($this->decoder)($this->in->parse($input, path: $path));

        return $this->out->parse($decoded, path: $path);
    }

    /**
     * Encodes the value: encodes it with the output schema, converts it back, and encodes the result with the input schema.
     *
     * @param mixed $input The decoded value.
     * @param string $path The path to the value, used for error reporting.
     * @return mixed The encoded value.
     * @throws SanitizrValidationException If either schema rejects its value.
     */
    protected function encodeValue(mixed $input, string $path = ''): mixed
    {
        $encoded = ($this->encoder)($this->out->encode($input, path: $path));

        return $this->in->encode($encoded, path: $path);
    }
}
