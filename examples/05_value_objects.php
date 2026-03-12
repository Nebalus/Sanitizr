<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

// 1. Define Value Objects using the Trait

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

    public static function from(string $email): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($email);

        if ($validData->isError()) {
            throw new SanitizrValidationException($validData->getErrorMessage());
        }

        return new self($validData->getValue());
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
        ])->transform(function (array $data) {
            return new self($data['mime_type'], $data['content']);
        });
    }

    public static function from(array $data): self
    {
        $validData = static::getSchema()->safeParse($data);
        if ($validData->isError()) {
            throw new SanitizrValidationException($validData->getErrorMessage());
        }
        return $validData->getValue(); // Value is already transformed to `MessageBody`
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
                'to' => S::array(Email::getSchema()->transform(fn($e) => Email::from($e))),
                'bcc' => S::array(Email::getSchema()->transform(fn($e) => Email::from($e)))->optional()->default([]),
                'cc' => S::array(Email::getSchema()->transform(fn($e) => Email::from($e)))->optional()->default([]),
            ])
        ])->transform(function (array $data) {
            return new self(
                $data['type'],
                $data['id'],
                $data['sender_alias'] ?? null,
                $data['recipients']['to'],
                $data['recipients']['bcc'],
                $data['recipients']['cc']
            );
        });
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
        ])->transform(function (array $data) {
            return new self(
                $data['type'],
                $data['id'],
                $data['sender_alias'] ?? null,
                $data['recipients']
            );
        });
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
            'channels' => S::array(S::discriminatedUnion('type', SmtpChannel::getSchema(), SmsChannel::getSchema())),
            'message' => S::object([
                'subject' => S::string(),
                'body' => S::array(MessageBody::getSchema())
            ])
        ])->transform(function (array $data) {
            return new self(
                $data['channels'],
                $data['message']['subject'],
                $data['message']['body']
            );
        });
    }

    public static function fromArray(array $data): self
    {
        $validData = static::getSchema()->safeParse($data);
        if ($validData->isError()) {
            echo "Error Parsing " . $validData->getErrorMessage() . "\n";
            throw new SanitizrValidationException($validData->getErrorMessage());
        }
        return $validData->getValue(); // Value is transformed into SendRequest!
    }
}

// 2. The mock JSON payload matching the user's example
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

// Parse and validate the structure
try {
    $requestDto = SendRequest::fromArray($inputData);
    
    echo "Payload is VALID and hydrated to Objects!\n\nParsed Data:\n";
    print_r($requestDto);
} catch (Exception $e) {
    echo "Payload is INVALID.\n";
    echo "Error: " . $e->getMessage() . "\n";
}
