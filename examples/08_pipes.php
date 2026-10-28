<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;

// A pipe parses the input with one schema, then parses that result with another.
// Use it to validate a value again after a transform has turned it into another type.

// ── 1. String → integer → range check ────────────────────────────────────────

echo "=== 1. Query parameter to integer ===\n";

$pageSchema = S::string()
    ->digits()
    ->transform(fn(string $value) => (int) $value)
    ->pipe(S::number()->integer()->gte(1)->lte(100));

var_dump($pageSchema->parse('42'));                                   // int(42)
echo $pageSchema->safeParse('abc')->getError()->getMessage() . "\n"; // fails in the first schema
echo $pageSchema->safeParse('500')->getError()->getMessage() . "\n"; // fails in the second schema

// ── 2. Comma-separated string → array of tags ────────────────────────────────

echo "\n=== 2. Comma-separated tags ===\n";

$tagsSchema = S::string()
    ->trim()
    ->transform(fn(string $value) => array_map('trim', explode(',', $value)))
    ->pipe(S::array(S::string()->min(2)->toLowerCase())->max(5));

print_r($tagsSchema->parse(' PHP, Zod , Validation '));               // ['php', 'zod', 'validation']
echo $tagsSchema->safeParse('php, x')->getError()->getMessage() . "\n"; // "1: ..." (the second tag is too short)

// ── 3. JSON string → validated object, inside a larger payload ───────────────

echo "\n=== 3. JSON column in a payload ===\n";

// S::pipe($in, $out) is the same as $in->pipe($out).
$settingsSchema = S::pipe(
    S::string()->transform(fn(string $value) => json_decode($value, true)),
    S::object([
        'theme' => S::enum(['light', 'dark']),
        'pageSize' => S::number()->integer()->positive(),
    ])
);

$rowSchema = S::object([
    'id' => S::number()->integer(),
    'settings' => $settingsSchema,
]);

print_r($rowSchema->parse([
    'id' => 1,
    'settings' => '{"theme":"dark","pageSize":25}',
]));

// Errors from the second schema keep the path of the property.
echo $rowSchema->safeParse([
    'id' => 2,
    'settings' => '{"theme":"blue","pageSize":25}',
])->getError()->getMessage() . "\n";                                   // "settings.theme: ..."

// ── 4. Pipes are schemas too ─────────────────────────────────────────────────

echo "\n=== 4. Modifiers and chaining ===\n";

// Modifiers apply to the pipe as a whole...
$optionalPage = $pageSchema->optional()->default(1);
print_r(S::object(['page' => $optionalPage])->parse([]));             // ['page' => 1]

// ...and pipes can be chained.
$evenPageSchema = $pageSchema->pipe(S::number()->multipleOf(2));
var_dump($evenPageSchema->safeParse('4')->isValid());                // bool(true)
var_dump($evenPageSchema->safeParse('5')->isValid());                // bool(false)
