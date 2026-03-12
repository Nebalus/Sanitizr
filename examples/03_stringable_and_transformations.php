<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;

// Sometimes data comes from $_GET or $_POST where everything is a string.
// `stringable()` allows strings to be automatically cast to numbers or booleans.
$querySchema = S::object([
    'page' => S::number()->integer()->stringable()->default(1),
    'limit' => S::number()->integer()->stringable()->default(20),
    'active' => S::boolean()->stringable()->default(true),

    // Applying data transformations
    'search' => S::string()->trim()->toLowerCase()->optional(),
]);

$rawQueryData = [
    'page' => '2',
    'limit' => '50',
    'active' => 'false',
    'search' => '   HELLO WORLD   '
];

$result = $querySchema->safeParse($rawQueryData);

if ($result->isValid()) {
    $data = $result->getValue();
    echo "Transformed Query Data:\n";
    var_dump($data['page']);     // int(2)
    var_dump($data['limit']);    // int(50)
    var_dump($data['active']);   // bool(false)
    var_dump($data['search']);   // string(11) "hello world"
} else {
    echo "Validation failed: " . $result->getErrorMessage() . "\n";
}
