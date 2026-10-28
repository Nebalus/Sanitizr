<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

// 1. Define value objects with the trait.
//
// Each class defines its schema once. The trait then provides:
//   - from() / tryFrom():        parse one input into an instance
//   - getValueObjectSchema():    the schema, yielding the instance, for use inside larger schemas
//   - getSchema():               the schema, yielding the plain value
//   - isValid():                 does an input satisfy the schema
// An instance is built as `new static($parsedValue)` unless fromParsed() is overridden.

class Email
{
    use SanitizrValueObjectTrait;

    private function __construct(
        private readonly string $email,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->email()->toLowerCase();
    }

    public function asString(): string
    {
        return $this->email;
    }
}

class MessageBody
{
    use SanitizrValueObjectTrait;

    private function __construct(
        public readonly string $mimeType,
        public readonly string $content,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::object([
            'mime_type' => S::string()->includes('/'), // basic check for mime type format
            'content' => S::string(),
        ]);
    }

    // The parsed value is an array, so the constructor takes it apart.
    protected static function fromParsed(mixed $value): static
    {
        return new self($value['mime_type'], $value['content']);
    }
}

class SmtpChannel
{
    use SanitizrValueObjectTrait;

    private function __construct(
        public readonly string $type,
        public readonly string $id,
        public readonly ?string $senderAlias,
        /** @var Email[] $to */
        public readonly array $to,
        /** @var Email[] $bcc */
        public readonly array $bcc,
        /** @var Email[] $cc */
        public readonly array $cc,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::object([
            'type' => S::literal('smtp'),
            'id' => S::string(),
            'sender_alias' => S::string()->optional()->nullable(),
            'recipients' => S::object([
                // Each address parses straight into an Email instance.
                'to' => S::array(Email::getValueObjectSchema()),
                'bcc' => S::array(Email::getValueObjectSchema())->optional()->default([]),
                'cc' => S::array(Email::getValueObjectSchema())->optional()->default([]),
            ])
        ]);
    }

    protected static function fromParsed(mixed $value): static
    {
        return new self(
            $value['type'],
            $value['id'],
            $value['sender_alias'] ?? null,
            $value['recipients']['to'],
            $value['recipients']['bcc'],
            $value['recipients']['cc']
        );
    }
}

class SmsChannel
{
    use SanitizrValueObjectTrait;

    private function __construct(
        public readonly string $type,
        public readonly string $id,
        public readonly ?string $senderAlias,
        /** @var string[] $recipients */
        public readonly array $recipients,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::object([
            'type' => S::literal('sms'),
            'id' => S::string(),
            'sender_alias' => S::string()->optional()->nullable(),
            'recipients' => S::array(S::string()), // expecting phone numbers
        ]);
    }

    protected static function fromParsed(mixed $value): static
    {
        return new self(
            $value['type'],
            $value['id'],
            $value['sender_alias'] ?? null,
            $value['recipients']
        );
    }
}

class SendRequest
{
    use SanitizrValueObjectTrait;

    private function __construct(
        /** @var array<SmtpChannel|SmsChannel> $channels */
        public readonly array $channels,
        public readonly string $subject,
        /** @var MessageBody[] $body */
        public readonly array $body,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::object([
            // A union of value-object schemas: each channel comes out as the class that matched.
            'channels' => S::array(S::union(SmtpChannel::getValueObjectSchema(), SmsChannel::getValueObjectSchema())),
            'message' => S::object([
                'subject' => S::string(),
                'body' => S::array(MessageBody::getValueObjectSchema())
            ])
        ]);
    }

    protected static function fromParsed(mixed $value): static
    {
        return new self(
            $value['channels'],
            $value['message']['subject'],
            $value['message']['body']
        );
    }
}

// 2. The mock JSON payload
$jsonPayload = '{
    "channels": [
        {
            "type": "smtp",
            "id": "sanitizr-noreply",
            "sender_alias": "Sanitizr",
            "recipients": {
                "to": ["user@Example.com"],
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

// 3. Parse the whole payload into a SendRequest — every nested value object is built on the way.
try {
    $requestDto = SendRequest::from($inputData);

    echo "Payload is VALID and hydrated to objects!\n\nParsed Data:\n";
    print_r($requestDto);
} catch (SanitizrValidationException $e) {
    echo "Payload is INVALID.\n";
    echo "Error: " . $e->getMessage() . "\n";
}

// 4. A single value: from() throws, tryFrom() answers null.
echo "\n" . Email::from('Alex@Example.com')->asString() . "\n";
var_dump(Email::tryFrom('not an email'));
