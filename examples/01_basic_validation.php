<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;

// Define a schema for a user registration form
$registerSchema = S::object([
    'username' => S::string()->min(3)->max(20)->toLowerCase(),
    'email' => S::string()->email(),
    'age' => S::number()->integer()->gte(18),
    'termsAccepted' => S::boolean(),
]);

$validInput = [
    'username' => 'JohnDoe',
    'email' => 'john.doe@example.com',
    'age' => 25,
    'termsAccepted' => true,
];

$invalidInput = [
    'username' => 'Jo', // too short
    'email' => 'not-an-email', // invalid email
    'age' => 17, // too young
    'termsAccepted' => false,
];

echo "--- Valid Input ---\n";
$result = $registerSchema->safeParse($validInput);
if ($result->isValid()) {
    print_r($result->getValue());
    // Note that username is now lowercase: johndoe
} else {
    echo "Error: " . $result->getErrorMessage() . "\n";
}

echo "\n--- Invalid Input ---\n";
$result = $registerSchema->safeParse($invalidInput);
if ($result->isValid()) {
    print_r($result->getValue());
} else {
    echo "Validation failed: " . $result->getErrorMessage() . "\n";
}
