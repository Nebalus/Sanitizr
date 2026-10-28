<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

// A codec is a conversion that works in both directions:
//   parse() / decode()  turns the wire format into your types,
//   encode()            turns your types back into the wire format.

// ── 1. String ↔ integer ──────────────────────────────────────────────────────

echo "=== 1. String <-> integer ===\n";

$stringToInt = S::codec(
    S::string()->digits(),   // the encoded form
    S::integer(),            // the decoded form
    decode: fn(string $value) => (int) $value,
    encode: fn(int $value) => (string) $value,
);

var_dump($stringToInt->decode('42'));  // int(42)
var_dump($stringToInt->encode(42));    // string(2) "42"

// Both sides are validated, in both directions.
echo $stringToInt->safeParse('4x2')->getError()->getMessage() . "\n";
echo $stringToInt->safeEncode('42x')->getError()->getMessage() . "\n";

// ── 2. Price string ↔ cents ──────────────────────────────────────────────────

echo "\n=== 2. Price string <-> cents ===\n";

$priceInCents = S::codec(
    S::string()->regex('/^\d+\.\d{2}$/', 'Must be a price like 19.99'),
    S::integer()->nonNegative(),
    decode: fn(string $value) => (int) round(((float) $value) * 100),
    encode: fn(int $cents) => sprintf('%d.%02d', intdiv($cents, 100), $cents % 100),
);

var_dump($priceInCents->decode('19.99'));  // int(1999)
var_dump($priceInCents->encode(1999));     // string(5) "19.99"

// ── 3. ISO date string ↔ DateTimeImmutable ───────────────────────────────────

echo "\n=== 3. ISO date <-> DateTimeImmutable ===\n";

// The decoded side needs a schema that accepts a DateTimeImmutable.
// A custom schema only has to implement parseValue(); encoding validates the same way.
class DateTimeSchema extends AbstractSanitizrSchema
{
    protected function parseValue(mixed $input, string $path = ''): DateTimeImmutable
    {
        if (! $input instanceof DateTimeImmutable) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: "Value must be a DateTimeImmutable",
                expected: 'DateTimeImmutable',
                received: get_debug_type($input),
            ));
        }

        return $input;
    }
}

$isoDatetime = S::codec(
    S::string(),
    new DateTimeSchema(),
    decode: fn(string $value) => new DateTimeImmutable($value),
    encode: fn(DateTimeImmutable $value) => $value->format(DATE_ATOM),
);

$date = $isoDatetime->decode('2026-10-28T12:30:00+00:00');
echo get_class($date) . ': ' . $date->format('l, j F Y') . "\n";
echo $isoDatetime->encode($date->modify('+1 day')) . "\n";

// ── 4. A whole payload, there and back again ─────────────────────────────────

echo "\n=== 4. Decode a request, encode a response ===\n";

// encode() walks the whole schema: objects, arrays, tuples and unions encode every
// element, so the codecs nested inside are reversed as well.
$orderSchema = S::object([
    'id' => $stringToInt,
    'placedAt' => $isoDatetime,
    'items' => S::array(S::object([
        'sku' => S::string(),
        'price' => $priceInCents,
    ])),
    'note' => S::string()->optional(),
]);

$request = [
    'id' => '1001',
    'placedAt' => '2026-10-28T12:30:00+00:00',
    'items' => [
        ['sku' => 'BOOK-1', 'price' => '19.99'],
        ['sku' => 'PEN-7', 'price' => '2.50'],
    ],
];

$order = $orderSchema->decode($request);

// Work with real types...
$total = array_sum(array_column($order['items'], 'price'));
echo "Order #{$order['id']}, total in cents: $total\n";   // 2249
$order['placedAt'] = $order['placedAt']->modify('+2 hours');

// ...and turn them back into the wire format.
print_r($orderSchema->encode($order));

// Encoding errors carry the path, like parsing errors do.
$order['items'][1]['price'] = 'free';
echo $orderSchema->safeEncode($order)->getError()->getMessage() . "\n";  // items.1.price: ...

// ── 5. transform() is one-way ────────────────────────────────────────────────

echo "\n=== 5. transform() cannot be encoded ===\n";

// A transform has no inverse, so encoding through one is a programming error.
// Use a codec instead when the conversion has to work both ways.
$upper = S::string()->transform(fn(string $value) => strtoupper($value));

try {
    $upper->encode('HELLO');
} catch (LogicException $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
