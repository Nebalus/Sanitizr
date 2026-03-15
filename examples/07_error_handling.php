<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

// ── 1. Basic error with structured issue ─────────────────────────────────────

echo "=== 1. Basic Type Error ===\n";

$schema = S::string()->min(3)->email();

$result = $schema->safeParse(42);

if ($result->isError()) {
    $error = $result->getError();

    // Flat message (like before)
    echo "Message: " . $error->getMessage() . "\n";

    // Structured issues
    foreach ($error->getIssues() as $issue) {
        echo sprintf(
            "  Code: %s | Message: %s | Expected: %s | Received: %s\n",
            $issue->code,
            $issue->message,
            $issue->expected ?? '-',
            $issue->received ?? '-',
        );
    }
}

// ── 2. Nested object paths ───────────────────────────────────────────────────

echo "\n=== 2. Nested Object Paths ===\n";

$userSchema = S::object([
    'username' => S::string()->min(3),
    'profile' => S::object([
        'age' => S::number()->integer()->gte(18),
        'address' => S::object([
            'city' => S::string()->min(2),
            'zip' => S::string()->regex('/^\d{5}$/', 'Must be a 5-digit zip code'),
        ]),
    ]),
]);

$invalidUser = [
    'username' => 'Jo',  // too short → path: "username"
    'profile' => [
        'age' => 15,     // too young → path: "profile.age"
        'address' => [
            'city' => 'A',   // too short → path: "profile.address.city"
            'zip' => 'abc',  // bad format → path: "profile.address.zip"
        ],
    ],
];

// Using parse() with try/catch — throws on first error
echo "-- parse() with try/catch:\n";
try {
    $userSchema->parse($invalidUser);
} catch (SanitizrValidationException $e) {
    foreach ($e->getError()->getIssues() as $issue) {
        echo sprintf("  [%s] %s → %s\n", $issue->code, $issue->getPathString(), $issue->message);
    }
}

// Using safeParse() — catches the first error without throwing
echo "\n-- safeParse() catches all errors:\n";
$result = $userSchema->safeParse($invalidUser);
if ($result->isError()) {
    foreach ($result->getError()->getIssues() as $issue) {
        echo sprintf("  [%s] %s → %s\n", $issue->code, $issue->getPathString(), $issue->message);
    }

    // You can also serialize to array for JSON APIs
    echo "\n  Serialized:\n" . json_encode($result->getError()->toArray(), JSON_PRETTY_PRINT) . "\n";
}

// ── 3. Discriminated Union with path ─────────────────────────────────────────

echo "\n=== 3. Discriminated Union Errors ===\n";

$eventSchema = S::object([
    'event' => S::discriminatedUnion(
        'type',
        S::object([
            'type' => S::literal('click'),
            'x' => S::number(),
            'y' => S::number(),
        ]),
        S::object([
            'type' => S::literal('keypress'),
            'key' => S::string(),
        ]),
    ),
]);

// Missing discriminator key
$result = $eventSchema->safeParse(['event' => ['x' => 10]]);
if ($result->isError()) {
    $issue = $result->getError()->getIssues()[0];
    echo sprintf("  Missing discriminator: [%s] %s → %s\n", $issue->code, $issue->getPathString(), $issue->message);
}

// Invalid discriminator value
$result = $eventSchema->safeParse(['event' => ['type' => 'scroll']]);
if ($result->isError()) {
    $issue = $result->getError()->getIssues()[0];
    echo sprintf("  Bad discriminator:     [%s] %s → %s\n", $issue->code, $issue->getPathString(), $issue->message);
}

// Valid discriminator but invalid nested field
$result = $eventSchema->safeParse(['event' => ['type' => 'click', 'x' => 'not-a-number', 'y' => 5]]);
if ($result->isError()) {
    $issue = $result->getError()->getIssues()[0];
    echo sprintf("  Bad nested field:      [%s] %s → %s\n", $issue->code, $issue->getPathString(), $issue->message);
}

// ── 4. Using getError() in an API response ───────────────────────────────────

echo "\n=== 4. JSON API Error Response ===\n";

$result = $userSchema->safeParse(['profile' => 'not-an-object']);
if ($result->isError()) {
    $response = [
        'success' => false,
        'errors' => $result->getError()->toArray(),
    ];
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
}
