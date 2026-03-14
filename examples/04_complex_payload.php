<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;

// 1. Define schemas for different target channel types

$smtpChannelSchema = S::object([
    'type' => S::literal('smtp'),
    'id' => S::string(),
    'sender_alias' => S::string()->optional(),
    'recipients' => S::object([
        'to' => S::array(S::string()->email()),
        'bcc' => S::array(S::string()->email())->optional()->default([]),
        'cc' => S::array(S::string()->email())->optional()->default([]),
    ])
]);

$smsChannelSchema = S::object([
    'type' => S::literal('sms'),
    'id' => S::string(),
    'sender_alias' => S::string()->optional(),
    'recipients' => S::array(S::string()), // expecting phone numbers
]);

// Use discriminatedUnion to explicitly route validation based on the `type` field
$channelSchema = S::discriminatedUnion('type', $smtpChannelSchema, $smsChannelSchema);

// 2. Define the schema for the message body contents
$messageBodySchema = S::object([
    'mime_type' => S::string()->includes('/'), // basic check for mime type format
    'content' => S::string(),
]);

// 3. Define the full POST request payload schema
$sendRequestSchema = S::object([
    'channels' => S::array($channelSchema),
    'message' => S::object([
        'subject' => S::string(),
        'body' => S::array($messageBodySchema)
    ])
]);


// The mock JSON payload matching the user's example
$jsonPayload = '{
    "channels": [
        {
            "type": "smtp",
            "id": "sanitizr-noreply",
            "sender_alias": "Sanitizr",
            "recipients": {
                "to": ["example@example.com"],
                "bcc": [],
                "cc": []
            }
        }, 
        {
            "type": "sms",
            "id": "sanitizr-noreply",
            "recipients": ["+1 1234 5678 910"]
        }  
    ],
    "message": {
        "subject": "lorem ipsum",
        "body": [
            {
                "mime_type": "text/html",
                "content": "<h1>Hello World</h1>"
            },
            {
                "mime_type": "text/plain",
                "content": "Hello World"
            }
        ]
    }
}';

// Decode JSON to an associative array
$inputData = json_decode($jsonPayload, true);

// Parse and validate the structure
$result = $sendRequestSchema->safeParse($inputData);

if ($result->isValid()) {
    echo "Payload is VALID!\n\nParsed Data:\n";
    print_r($result->getValue());
} else {
    echo "Payload is INVALID.\n";
    echo "Error: " . $result->getError()->getMessage() . "\n";
}
