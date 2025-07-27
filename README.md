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
    'email' => S::string()->email(),
    'age' => S::int()->min(0)->optional(),
]);

// Define input
$input = [
    'name' => 'Alex',
    'email' => 'alex@example.com',
];

$result = $userSchema->safeParse($input);

if ($result->isSuccess()) {
    $user = $result->getValue();
    // Use sanitized data
    echo $user["name"]; // Outputs: Alex
    echo $user["email"]; // Outputs: alex@example.com
} else {
    $errorMessage = $result->getErrorMessage();
    // Handle validation errors
}
```

### Advanced Usage

- **Custom filters:**  
  Create your own filters and add them to schemas.
- **Nested objects & arrays:**  
  Compose validators for deeply nested data.

See the [examples directory](https://github.com/Nebalus/Sanitizr/tree/dev/examples) and [API Reference](https://github.com/Nebalus/Sanitizr/wiki) for more details.

## Credits

- Inspired by [Zod](https://zod.dev) (TypeScript) and the wider PHP validation ecosystem.
- Created and maintained by [Nebalus](https://github.com/Nebalus).

## Related

- [Zod (TypeScript)](https://zod.dev)

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/Nebalus/Sanitizr/blob/dev/LICENSE) file for details.
