# Contributing to Sanitizr

First off, thanks for taking the time to contribute! 🎉

The following is a set of guidelines for contributing to Sanitizr. These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [I don't want to read this whole thing, I just have a question!](#i-dont-want-to-read-this-whole-thing-i-just-have-a-question)
- [What should I know before I get started?](#what-should-i-know-before-i-get-started)
- [How Can I Contribute?](#how-can-i-contribute)
  - [Reporting Bugs](#reporting-bugs)
  - [Suggesting Enhancements](#suggesting-enhancements)
  - [Pull Requests](#pull-requests)
- [Styleguides](#styleguides)
  - [Git Commit Messages](#git-commit-messages)
  - [PHP Styleguide](#php-styleguide)
  - [Documentation Styleguide](#documentation-styleguide)
- [Development Setup](#development-setup)

## Code of Conduct

This project and everyone participating in it is governed by our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to the maintainers.

## I don't want to read this whole thing, I just have a question!

> **Note:** Please don't file an issue to ask a question. You'll get faster results by using the resources below.

- [GitHub Discussions](https://github.com/Nebalus/Sanitizr/discussions) - Ask questions and discuss ideas
- [GitHub Issues](https://github.com/Nebalus/Sanitizr/issues) - Search existing issues to see if your question has already been answered
- [API Reference](https://github.com/Nebalus/Sanitizr/wiki) - Check the documentation

## What should I know before I get started?

### Sanitizr Project

Sanitizr is a Zod-inspired validation and filtering framework written in PHP. It provides:

- A fluent, Zod-like API for defining schemas
- Composable validators built from primitives and custom rules
- Zero dependencies for lightweight integration
- Extensibility for custom filters and validation logic

The project structure includes:

- **Core validators:** String, int, float, bool, object, array, and more
- **Filters:** Transform and sanitize data
- **Schema composition:** Build complex validators from primitives
- **Error handling:** Clear error messages and validation results

### Repository Structure

```
├── src/
│   ├── Validators/      # Core validator classes
│   ├── Filters/         # Filter implementations
│   ├── Results/         # Result and error handling
│   └── SanitizrStatic.php  # Static API entry point
├── tests/               # Test suite
├── examples/            # Usage examples
├── docs/                # Documentation
└── composer.json        # Project dependencies and metadata
```

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the [issue list](https://github.com/Nebalus/Sanitizr/issues) as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

**Describe the bug:**
- A clear and concise description of what the bug is
- Specific examples to demonstrate the steps

**Expected behavior:**
- A clear and concise description of what you expected to happen

**Screenshots and code samples:**
- Code samples that demonstrate the problem (in a code block)
- Screenshots if applicable

**Environment:**
- PHP version
- Composer dependencies version
- OS and version

**Example bug report:**

```
Title: String validator incorrectly rejects valid email addresses

Description:
When using S::string()->email(), the validator rejects valid email addresses...

Steps to reproduce:
1. Create a validator: $schema = S::string()->email();
2. Validate: $schema->parse('test@example.com');
3. Result: Throws validation error

Expected: Should accept the email as valid
Actual: Throws error "Invalid email format"

Environment:
- PHP 8.1
- Sanitizr 1.0.0
```

### Suggesting Enhancements

Enhancement suggestions are tracked as [GitHub issues](https://github.com/Nebalus/Sanitizr/issues). When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and the proposed behavior**
- **Explain why this enhancement would be useful**

### Pull Requests

- Fill in the required template
- Follow the [PHP Styleguide](#php-styleguide)
- Include appropriate test cases
- End all files with a newline
- Avoid platform-specific code
- Provide a clear description of your changes

**Process for submitting a PR:**

1. Fork the repository
2. Create a branch for your feature or fix: `git checkout -b feature/my-feature` or `git checkout -b fix/my-fix`
3. Make your changes following the styleguide
4. Add or update tests as necessary
5. Run tests to ensure everything passes: `composer test`
6. Commit your changes: `git commit -am 'Add/fix: descriptive message'`
7. Push to your fork: `git push origin feature/my-feature`
8. Create a Pull Request with a clear description

## Styleguides

### Git Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line
- Consider starting the commit message with an applicable emoji:
  - 🎨 `:art:` - Improve structure/format of the code
  - 🐛 `:bug:` - Fix a bug
  - ✨ `:sparkles:` - Introduce new features
  - 📚 `:books:` - Documentation
  - 🔧 `:wrench:` - Add or update configuration files
  - 🧪 `:test_tube:` - Add tests
  - 📦 `:package:` - Add or update dependencies
  - ♻️ `:recycle:` - Refactor code

**Examples:**
```
✨ Add optional() method to validators
🐛 Fix string validator min/max length handling
📚 Update API reference documentation
🧪 Add tests for nested object validation
```

### PHP Styleguide

- Follow [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/) standards
- Use type hints for all parameters and return types
- Use strict types: `declare(strict_types=1);` at the top of each file
- Use meaningful variable names
- Add PHPDoc comments for public methods

**Example:**

```php
declare(strict_types=1);

namespace Nebalus\Sanitizr\Validators;

/**
 * Validates string values with various options
 */
class StringValidator implements ValidatorInterface
{
    /**
     * Validate that the value is a string with minimum length
     * 
     * @param mixed $value The value to validate
     * @param int $minLength Minimum required length
     * @return bool True if valid, false otherwise
     */
    public function validateMinLength(mixed $value, int $minLength): bool
    {
        return is_string($value) && strlen($value) >= $minLength;
    }
}
```

### Documentation Styleguide

- Use Markdown for documentation
- Use clear, concise language
- Include code examples where appropriate
- Link to related documentation
- Keep documentation up-to-date with code changes

## Development Setup

### Prerequisites

- PHP 8.0 or higher
- Composer

### Installation

1. Fork and clone the repository:
   ```bash
   git clone https://github.com/YOUR-USERNAME/Sanitizr.git
   cd Sanitizr
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Run tests to verify setup:
   ```bash
   composer test
   ```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Run specific test file
vendor/bin/phpunit tests/Validators/StringValidatorTest.php
```

### Useful Commands

```bash
# Run code style checker
composer lint

# Fix code style issues
composer lint:fix

# Run static analysis
composer analyze
```

## Recognition

Contributors will be recognized in:
- The README.md file
- GitHub's contributors page
- Release notes for their contributions

Thank you for contributing to Sanitizr! 🚀
