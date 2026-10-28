<h1 align="center">
  <br>
  <a href="https://github.com/Nebalus/Sanitizr"><img src="imgs/logo.jpg?raw=true" alt="Sanitizr" width="70%"></a>
  <br>
</h1>

<h4 align="center">Schema validation and sanitization for PHP, inspired by <a href="https://zod.dev">Zod</a>.</h4>

<p align="center">
  <a href="#installation">Installation</a> •
  <a href="#quick-start">Quick Start</a> •
  <a href="#schemas">Schemas</a> •
  <a href="#modifiers">Modifiers</a> •
  <a href="#pipes">Pipes</a> •
  <a href="#codecs">Codecs</a> •
  <a href="#value-objects">Value Objects</a> •
  <a href="#errors">Errors</a>
</p>

Describe the shape of your data once. Sanitizr checks untrusted input against it, cleans it up, and gives you
either a value you can trust or a structured list of everything that was wrong with it.

- **Fluent and immutable.** Every method returns a new schema, so a schema can be shared and extended without side effects.
- **Every error at once.** Objects and arrays collect all issues, each with its path, ready to send back as JSON.
- **Validation and sanitization in one place.** Trim, change case, strip tags or coerce form strings while validating.
- **Both directions.** Codecs decode wire formats into your types and encode them back again.
- **Zero dependencies.** Plain PHP 8.3+.

## Installation

```bash
composer require nebalus/sanitizr
```

Requires PHP 8.3 or newer.

## Quick Start

```php
use Nebalus\Sanitizr\SanitizrStatic as S;

$userSchema = S::object([
    'name'  => S::string()->trim()->min(1),
    'email' => S::email(),
    'age'   => S::integer()->gte(0)->optional(),
    'role'  => S::enum(['admin', 'user'])->default('user'),
]);

$result = $userSchema->safeParse([
    'name'  => '  Alex ',
    'email' => 'alex@example.com',
]);

if ($result->isValid()) {
    $user = $result->getValue(); // ['name' => 'Alex', 'email' => 'alex@example.com', 'role' => 'user']
} else {
    $issues = $result->getError()->toArray(); // one entry per problem, with its path
}
```

`parse()` returns the value or throws a `SanitizrValidationException`; `safeParse()` never throws and returns a
`SafeParsedData` with `isValid()`, `getValue()` and `getError()`.

### Static or instance API

`SanitizrStatic` (usually aliased as `S`) and `Sanitizr` offer the same factory methods. Use whichever fits
your code, for example inject `Sanitizr` where you prefer instances:

```php
use Nebalus\Sanitizr\Sanitizr;

$z = new Sanitizr();
$schema = $z->object(['status' => $z->enum(['active', 'banned'])]);
```

## Schemas

### Strings

```php
S::string()->min(3)->max(50);
S::email();                 // shorthand for S::string()->email()
```

| Kind | Methods |
|---|---|
| Length (in bytes) | `length($n)`, `min($n)`, `max($n)`, `between($min, $max)` |
| Content | `regex($pattern)`, `includes($needle)`, `startsWith($prefix)`, `endsWith($suffix)`, `uppercase()`, `lowercase()`, `alphanumeric()`, `digits()`, `emoji()` |
| Formats | `email()`, `url()`, `httpUrl()`, `hostname()`, `phone()`, `uuid()`, `ulid()`, `nanoid()`, `cuid()`, `cuid2()`, `jwt()`, `base64()`, `base64url()`, `hex()` |
| Network | `ipv4()`, `ipv6()`, `cidrv4()`, `cidrv6()`, `mac()` |
| Hashes | `md5()`, `sha1()`, `sha224()`, `sha256()`, `sha384()`, `sha512()`, `hash($algorithm)`, `bcrypt()`, `argon2i()`, `argon2id()` |
| Sanitizing | `trim()`, `toLowerCase()`, `toUpperCase()`, `toTitleCase()`, `stripTags($allowed)`, `htmlSpecialChars()` |

Every check takes an optional custom message as its last argument: `S::string()->min(3, 'Too short')`.
Sanitizing methods run before the checks, so `S::string()->trim()->min(1)` rejects a string of spaces.

### Numbers

```php
S::number();                // int or float
S::integer();               // int only
S::float();                 // float only

S::number()->gt(0)->lte(100)->multipleOf(5);
```

`gt()`, `gte()`, `lt()`, `lte()`, `positive()`, `nonNegative()`, `negative()`, `nonPositive()`, `multipleOf()`,
`integer()`, `float()`.

### Booleans, literals, enums and null

```php
S::boolean();
S::literal('admin');        // exactly this value
S::enum(['draft', 'published', 'archived']);
S::null();
```

### Form and query strings

Values from `$_GET`, `$_POST` or CSV files arrive as strings. `stringable()` on numbers and booleans converts them:

```php
$query = S::object([
    'page'   => S::integer()->stringable()->default(1),  // '2'     → 2
    'active' => S::boolean()->stringable(),               // 'false' → false
]);
```

### Objects

```php
$address = S::object([
    'street' => S::string(),
    'zip'    => S::string()->digits()->length(5),
]);

$customer = S::object([
    'name'    => S::string(),
    'address' => $address,
    'billing' => $address->nullable(),
]);
```

Accepts associative arrays and objects (via their public properties) and returns an array. Keys the schema
does not define are dropped. A missing key fails with `missing_key` unless its schema is `optional()`, has a
`default()` or a `catch()`.

### Arrays and tuples

```php
S::array(S::string())->min(1)->max(10);  // also: between(), notEmpty(), empty()
S::tuple(S::number(), S::number());       // one schema per position
```

A tuple parses only the positions it defines. A missing position fails with `missing_key`, but extra elements
are not rejected: they are dropped, so `[1, 2, 3]` parses to `[1, 2]`. To reject them, check the length of the
raw input first: `S::array(S::number())->max(2)->pipe(S::tuple(S::number(), S::number()))`.

### Unions

```php
S::union(S::string(), S::number());     // the first schema that accepts the input wins
S::string()->or(S::number());           // the same, written as a fallback
```

`and()` additionally requires the parsed value to satisfy another schema.

## Modifiers

Modifiers work on every schema and return a new one.

| Modifier | Effect |
|---|---|
| `optional()` | The key may be missing from an object; it is then left out of the result. |
| `nullable()` | `null` is accepted and returned as `null`. |
| `nullish()` | Both of the above. |
| `default($value)` | Used when the input is `null` or the key is missing. |
| `catch($value)` | Returned instead of failing. A `Closure` is called with the `SanitizrError` and the input. |
| `transform($fn)` | Maps the value after all checks have passed. |

```php
$settings = S::object([
    'theme'    => S::enum(['light', 'dark'])->catch('light'),     // anything invalid becomes 'light'
    'pageSize' => S::integer()->stringable()->default(20),
    'tags'     => S::string()->transform(fn(string $v) => explode(',', $v)),
]);
```

## Pipes

A pipe parses the input with one schema, then parses the result with another. That lets you validate a value
again after a transform has changed its type. `pipe()` is available on every schema:

```php
$page = S::string()
    ->digits()
    ->transform(fn(string $v) => (int) $v)
    ->pipe(S::integer()->gte(1)->lte(100));

$page->parse('42');   // 42
$page->parse('500');  // fails: Must be less than or equal to 100

S::pipe($in, $out);   // the same as $in->pipe($out)
```

Pipes are schemas themselves, so they take modifiers and can be chained: `$page->pipe(...)->optional()`.

## Codecs

A codec is a conversion that works in both directions. `parse()` (or `decode()`) turns the wire format into
your types, and `encode()` turns them back:

```php
$priceInCents = S::codec(
    S::string()->regex('/^\d+\.\d{2}$/'),  // encoded form
    S::integer()->nonNegative(),           // decoded form
    // integer arithmetic only; past PHP_INT_MAX the result becomes a float, which S::integer() rejects
    decode: fn(string $v) => (int) strstr($v, '.', true) * 100 + (int) substr($v, -2),
    encode: fn(int $cents) => sprintf('%d.%02d', intdiv($cents, 100), $cents % 100),
);

$priceInCents->decode('19.99'); // 1999
$priceInCents->encode(1999);    // '19.99'
```

`encode()` walks the whole schema. Objects, arrays, tuples and unions encode each element, pipes run in reverse,
and codecs call their `encode` function. So one schema can read a request and write the response:

```php
$order = S::object([
    'items' => S::array(S::object(['sku' => S::string(), 'price' => $priceInCents])),
]);

$data = $order->decode($request);  // prices are integers
$json = $order->encode($data);     // prices are strings again
```

Schemas without a codec validate the value when encoding, and still apply their sanitizing steps such as `trim()`
or `toLowerCase()`, so `S::string()->trim()->encode('  abc ')` returns `'abc'`. A `transform()` or a value object
schema cannot be reversed, so encoding through one throws a `LogicException`; use a codec when a conversion has to
work both ways. `safeEncode()` is the non-throwing variant.

## Value Objects

`SanitizrValueObjectTrait` gives a class a schema plus constructors that parse straight into an instance:

```php
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;

final class Email
{
    use SanitizrValueObjectTrait;

    private function __construct(public readonly string $value) {}

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->trim()->toLowerCase()->email();
    }
}

Email::from('  Alex@Example.com ');  // Email holding 'alex@example.com', throws if invalid
Email::tryFrom('not an email');      // null
Email::isValid('alex@example.com');  // true
```

Use `getValueObjectSchema()` inside larger schemas to get instances in the parsed result. `getSchema()` still
gives you the plain value:

```php
$schema = S::object([
    'contact' => Email::getValueObjectSchema(),
    'cc'      => S::array(Email::getValueObjectSchema())->default([]),
]);

$schema->parse(['contact' => 'alex@example.com'])['contact']; // an Email
```

The instance is built with `new static($value)`. A class whose constructor takes more than the parsed value,
for example one built from an object schema, overrides the protected `fromParsed(mixed $value): static` hook.
The schema is built once per class and cached, and an abstract base class can use the trait for all of its
subclasses.

## Errors

A failed parse produces a `SanitizrError` holding one `SanitizrIssue` per problem:

```php
use Nebalus\Sanitizr\Exception\SanitizrValidationException;

try {
    $userSchema->parse($input);
} catch (SanitizrValidationException $e) {
    $error = $e->getError();

    $error->getMessage(); // "email: Not a valid email address\nage: Value must be NUMERIC"
    $error->toArray();    // ready for a JSON response, see below

    foreach ($error->getIssues() as $issue) {
        $issue->code;              // 'invalid_string'
        $issue->getPathString();   // 'email'
    }
}
```

```json
[
  { "code": "invalid_string", "path": ["email"], "message": "Not a valid email address" },
  { "code": "invalid_type", "path": ["age"], "message": "Value must be NUMERIC", "expected": "number", "received": "string" }
]
```

Issue codes are available as constants on `SanitizrIssue`: `INVALID_TYPE`, `TOO_SMALL`, `TOO_BIG`,
`INVALID_STRING`, `INVALID_LITERAL`, `INVALID_ENUM_VALUE`, `MISSING_KEY`, `INVALID_UNION`, `NOT_MULTIPLE_OF`,
`CUSTOM`.

## Custom Schemas

Extend `AbstractSanitizrSchema` and implement `parseValue()`. Throw a `SanitizrValidationException` for invalid
input. All modifiers, pipes and codecs then work with your schema:

```php
use Nebalus\Sanitizr\Error\SanitizrIssue;
use Nebalus\Sanitizr\Exception\SanitizrValidationException;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;

final class DateTimeSchema extends AbstractSanitizrSchema
{
    protected function parseValue(mixed $input, string $path = ''): DateTimeImmutable
    {
        if (! $input instanceof DateTimeImmutable) {
            throw SanitizrValidationException::fromIssue(new SanitizrIssue(
                code: SanitizrIssue::INVALID_TYPE,
                path: self::pathToArray($path),
                message: 'Value must be a DateTimeImmutable',
            ));
        }

        return $input;
    }
}
```

Override `encodeValue()` as well if encoding should do something other than validate.

## Examples

The [examples directory](examples) has runnable scripts for each topic:

| Script | Topic |
|---|---|
| [`01_basic_validation.php`](examples/01_basic_validation.php) | Parsing and validating simple values |
| [`02_nested_objects.php`](examples/02_nested_objects.php) | Nested objects and arrays |
| [`03_stringable_and_transformations.php`](examples/03_stringable_and_transformations.php) | Query strings, sanitizing and transforms |
| [`04_complex_payload.php`](examples/04_complex_payload.php) | A larger payload with unions |
| [`05_value_objects.php`](examples/05_value_objects.php) | Value objects |
| [`06_enum_validation.php`](examples/06_enum_validation.php) | Enums |
| [`07_error_handling.php`](examples/07_error_handling.php) | Working with errors |
| [`08_pipes.php`](examples/08_pipes.php) | Pipes |
| [`09_codecs.php`](examples/09_codecs.php) | Codecs |

```bash
php examples/09_codecs.php
```

## Development

```bash
composer install
vendor/bin/phpunit           # tests
vendor/bin/phpcs src         # coding standard
vendor/bin/phpmd src text phpmd.xml
```

With Docker and [just](https://github.com/casey/just) installed, `just test` and `just lint` run the same in a container.
See [CONTRIBUTING.md](.github/CONTRIBUTING.md) before opening a pull request, and the [CHANGELOG](CHANGELOG.md) for
what changed between versions.

## Credits

- Inspired by [Zod](https://zod.dev) (TypeScript) and the wider PHP validation ecosystem.
- Created and maintained by [Nebalus](https://github.com/Nebalus).

## License

MIT. See [LICENSE](LICENSE).
