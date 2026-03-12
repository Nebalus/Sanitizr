<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\Sanitizr;

$z = new Sanitizr();

// Define a schema with an enum
$userStatusSchema = $z->enum(['active', 'pending', 'banned']);

echo "Validating 'active':\n";
try {
    $result = $userStatusSchema->parse('active');
    echo "Result: $result (Success)\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nValidating 'deleted' (Invalid):\n";
try {
    $userStatusSchema->parse('deleted');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . " (Expected)\n";
}

// Complex object example
$userSchema = $z->object([
    'name' => $z->string(),
    'role' => $z->enum(['admin', 'user', 'guest']),
    'level' => $z->enum([1, 2, 3])
]);

$payload = [
    'name' => 'John Doe',
    'role' => 'admin',
    'level' => 2
];

echo "\nValidating payload with role and level enums:\n";
try {
    $parsed = $userSchema->parse($payload);
    print_r($parsed);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
