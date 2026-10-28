<h1 align="center">
  <br>
  <a href="https://github.com/Nebalus/Sanitizr"><img src="imgs/logo.jpg?raw=true" alt="Sanitizr" width="70%"></a>
  <br>
</h1>

<h4 align="center">A <a href="https://zod.dev">Zod</a> inspired Validation and Filter Framework written in PHP.</h4>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#how-to-use">How To Use</a> •
  <a href="#credits">Credits</a> •
  <a href="#related">Related</a> •
  <a href="#license">License</a>
</p>

## Key Features

- **Zod-inspired API:** Familiar, fluent interface for schema definitions, validation, and filtering.
- **Composable schemas:** Build complex validators from primitives and custom rules.
- **Extensible:** Easily extend with your own filters and validation logic.
- **Zero dependencies:** Lightweight and easy to integrate.

## How To Use

### Installation

```bash
composer require nebalus/sanitizr
```

### Basic Example

```php
use Nebalus\Sanitizr\SanitizrStatic as S;

// Define a schema
$userSchema = S::object([
    'name' => S::string()->min(1),
    'email' => S::email(),
    'age' => S::int()->min(0)->optional(),
]);

// Define input
$input = [
    'name' => 'Alex',
    'email' => 'alex@example.com',
];

$result = $userSchema->safeParse($input);

if ($result->isValid()) {
    $user = $result->getValue();
    // Use sanitized data
    echo $user["name"]; // Outputs: Alex
    echo $user["email"]; // Outputs: alex@example.com
} else {
    $errorMessage = $result->getErrorMessage();
    // Handle validation errors
}
```

### Value Objects

Give a class a schema with `SanitizrValueObjectTrait`, and it gains constructors that parse straight into an instance — and a schema that yields the instance when used inside a larger one:

```php
use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

final class Email
{
    use SanitizrValueObjectTrait;

    private function __construct(private readonly string $value) {}

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->trim()->toLowerCase()->email();
    }

    public function asString(): string { return $this->value; }
}

$email = Email::from('  Alex@Example.com ');   // Email, holding "alex@example.com"
Email::tryFrom('not an email');                 // null
Email::isValid('alex@example.com');             // true

$schema = S::object([
    'contact' => Email::getValueObjectSchema(),
    'cc' => S::array(Email::getValueObjectSchema())->optional()->default([]),
]);
$parsed = $schema->parse(['contact' => 'alex@example.com']);
$parsed['contact'];                             // an Email, not a string
```

`Email::getSchema()` still yields the plain string. A class whose constructor takes the parsed value apart (an object schema, say) overrides the protected `fromParsed(mixed $value): static` hook.

### Advanced Usage

- **Custom filters:**  
  Create your own filters and add them to schemas.
- **Nested objects & arrays:**  
  Compose validators for deeply nested data.

### Codecs

A codec is a conversion that works in both directions. `parse()` (or `decode()`) turns the wire format into
your types, and `encode()` turns them back. Encoding walks the whole schema, so a codec nested in an object
or array is reversed as well:

```php
$isoDatetime = S::codec(
    S::string(),
    $dateTimeSchema, // any schema accepting a DateTimeImmutable
    decode: fn(string $value) => new DateTimeImmutable($value),
    encode: fn(DateTimeImmutable $value) => $value->format(DATE_ATOM),
);

$eventSchema = S::object(['startsAt' => $isoDatetime]);

$event = $eventSchema->parse(['startsAt' => '2026-10-28T12:30:00+00:00']); // startsAt is a DateTimeImmutable
$payload = $eventSchema->encode($event);                                   // startsAt is a string again
```

Schemas without a codec validate the value as they do when parsing. A `transform()` cannot be reversed, so
encoding through one throws a `LogicException`.

See the [examples directory](https://github.com/Nebalus/Sanitizr/tree/dev/examples) and [API Reference](https://github.com/Nebalus/Sanitizr/wiki) for more details.

## Credits

- Inspired by [Zod](https://zod.dev) (TypeScript) and the wider PHP validation ecosystem.
- Created and maintained by [Nebalus](https://github.com/Nebalus).

## Related

- [Zod (TypeScript)](https://zod.dev)

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/Nebalus/Sanitizr/blob/dev/LICENSE) file for details.
