<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;

$addressSchema = S::object([
    'street' => S::string()->min(5),
    'city' => S::string()->min(2),
    'zipCode' => S::string()->regex('/^\d{5}$/', "Must be exactly 5 digits"),
]);

$userSchema = S::object([
    'name' => S::string()->min(2),
    'address' => $addressSchema,
    'tags' => S::array(S::string()),
]);

$input = [
    'name' => 'Alice',
    'address' => [
        'street' => '123 Main St',
        'city' => 'Wonderland',
        'zipCode' => '12345'
    ],
    'tags' => ['admin', 'moderator']
];

$result = $userSchema->safeParse($input);

if ($result->isValid()) {
    echo "Parsed successfully:\n";
    print_r($result->getValue());
} else {
    echo "Parsing failed: " . $result->getErrorMessage() . "\n";
}
