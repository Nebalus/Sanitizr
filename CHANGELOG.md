# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)

## [Unreleased]


## [2.3.0] - 2026-10-28
### Added
- Codecs: `S::codec($in, $out, decode: ..., encode: ...)` returns a `SanitizrCodec`, a conversion that works in both directions. Parsing validates the input with `$in`, converts it with `decode` and validates the result with `$out`; encoding runs the other way round.
- `encode()`, `safeEncode()` and `decode()` (an alias of `parse()`) on every schema. Encoding walks the whole schema: objects, arrays, tuples and unions encode their elements, pipes run in reverse and codecs run their encode function, while every other schema validates the value as it does when parsing. Encoding through a `transform()` or a `SanitizrValueObject` throws a `LogicException`, since neither can be reversed.
- `encodeValue()`: the protected counterpart of `parseValue()` for custom schemas; it validates as `parseValue()` by default.
- Pipes: `$schema->pipe($other)` and `S::pipe($in, $out)` return a `SanitizrPipe` that parses the input with the first schema and then parses its result (after transforms) with the second, e.g. `S::string()->transform(intval(...))->pipe(S::number()->gt(0))`. Modifiers such as `optional()`, `nullable()`, `default()` and `transform()` apply to the pipe as a whole.
- `catch(mixed $value)` on every schema: returns the given fallback instead of failing when the input does not satisfy the schema (after any `or()` fallbacks). A `Closure` is called with the `SanitizrError` and the original input to compute the fallback; any other value is returned as it is. Inside an object or tuple schema a missing key is caught as well, so `S::object(['role' => S::string()->catch('user')])` parses `[]` to `['role' => 'user']`.
- `hasCatchValue()` on every schema.
- `S::email()` (and `Sanitizr::email()`): shorthand for `S::string()->email()`, taking the same optional custom error message.
- `SanitizrValueObject`: a schema node that yields an instance of a class instead of the parsed value. It wraps the schema that validates and normalises the raw input plus a factory that builds the object, and runs the factory inside `parseValue()` — so the object is produced wherever the node sits (object property, array item, union or tuple member), and the node's own `optional()`, `nullable()` and `default()` are answered before the factory is called.
- `SanitizrValueObjectTrait::getValueObjectSchema()`: the class's schema wrapped in a `SanitizrValueObject`, for use inside larger schemas (`S::object(['id' => UserId::getValueObjectSchema()])` parses to an array carrying a `UserId`).
- `SanitizrValueObjectTrait::from()` and `tryFrom()`: parse one input straight into an instance, throwing or answering null on an invalid input.
- `SanitizrValueObjectTrait::fromParsed()`: the protected hook that builds the instance from the parsed value (`new static($value)` by default), to override when the constructor takes the value apart.
- `SanitizrArray::getItemSchema()`, matching `SanitizrObject::getPropertySchemas()`.
- Examples `08_pipes.php` and `09_codecs.php`.

### Changed
- README rewritten: quick start, a reference for every schema and modifier, pipes, codecs, value objects, error handling, custom schemas, the examples and development setup. The old example used `S::int()` and `->min()` on a number, neither of which exists.
- `AbstractSanitizrSchema` has new public methods (`catch()`, `hasCatchValue()`, `pipe()`, `encode()`, `decode()`, `safeEncode()`) and new protected ones (`encodeValue()`, `runChild()`, `resolveCatchValue()`, `parseMissingKey()`). A custom schema that already declares a method of the same name with a different signature has to rename it.
- `SanitizrValueObjectTrait` has new methods (`from()`, `tryFrom()`, `getValueObjectSchema()`, `fromParsed()`). A subclass of a class using the trait that declares one of them with an incompatible signature has to rename it.

### Fixed
- An object schema used as a property of another object schema is now parsed in full. `SanitizrObject` used to call the nested schema's `parseValue()` directly, which skipped its `transform()`, `nullable()`, `default()`, checks and `and()`/`or()`, so a value object's schema used as a property (`S::object(['address' => Address::getSchema()])`) returned the raw array instead of the instance. Wrapping the schema in `S::union()` only hid the problem. Code that relied on those modifiers being ignored on nested objects now sees them applied.

[2.3.0]: https://github.com/Nebalus/Sanitizr/compare/v2.2.1...v2.3.0


## [2.2.1] - 2026-09-13
### Fixed
- `SanitizrValueObjectTrait` now works when the trait is used by a base class that several value objects extend. Previously `getSchema()` resolved `defineSchema()` with `self::`, which threw "Cannot call abstract method" on an abstract base, and its single static cache slot was shared by every subclass, so whichever subclass was touched first silently decided the schema all the others validated against. The cache is now keyed per concrete class and the schema is resolved late-bound.

[2.2.1]: https://github.com/Nebalus/Sanitizr/compare/v2.2.0...v2.2.1


## [2.2.0] - 2026-09-13
### Added
- Dedicated hash format validations on `SanitizrString`: `md5()`, `sha1()`, `sha224()`, `sha256()`, `sha384()` and `sha512()`.
- Password hash format validations on `SanitizrString`: `bcrypt()`, `argon2i()` and `argon2id()`.

### Fixed
- `SanitizrString::length()` reported the issue code `too_small` even when the input was too long; it now reports `too_big` in that case, matching `between()`.

### Changed
- `SanitizrString::hash()` now also accepts `sha224` and matches the algorithm name case-insensitively.
- **Breaking:** `SanitizrString::hash()` now rejects an unknown algorithm name with an `InvalidArgumentException` when the schema is built, instead of on the first `parse()` call.
- Hash validation issues now carry `expected` (e.g. `sha256 hash`) and `received` (e.g. `length:63`) details.

[2.2.0]: https://github.com/Nebalus/Sanitizr/compare/v2.1.1...v2.2.0


## [2.1.1] - 2026-09-05
### Added
- isValid() Methode in the SanitizrValueObjectTrait

[2.1.1]: https://github.com/Nebalus/Sanitizr/compare/v2.1.0...v2.1.1


## [2.1.0] - 2026-03-18
> **Note:** This tag points to the same commit as `v1.3.0`, which was incorrectly released as a minor version despite containing breaking changes.

### Added
- Comprehensive string format validations mimicking Zod (e.g., `uuid`, `cuid2`, `ulid`, `ipv4`, `ipv6`, `mac`, `base64`, `emoji`, `jwt`, `hash`, `cidrv4`, `cidrv6`, etc.) to `SanitizrString`.
- Array length constraints and validations (`min`, `max`, `between`, `notEmpty`, `empty`) to `SanitizrArray`.

### Changed
- Refactored `SanitizrDiscriminatedUnion` to a generalized `SanitizrUnion` schema allowing flexible multi-schema validation without strict discriminators.
- Moved Union Schema structure into a dedicated folder.

[2.1.0]: https://github.com/Nebalus/Sanitizr/compare/v2.0.0...v2.1.0


## [2.0.0] - 2026-05-13
> **Note:** This tag points to the same commit as `v1.2.0`, which was incorrectly released as a minor version despite containing breaking changes.

### Added
- Zod-style structured error handling using `SanitizrError` and `SanitizrIssue` for more informative validation errors.
- `SanitizrEnum` schema for validating values against PHP enums.
- Static and instance methods `enum()` in `Sanitizr` and `SanitizrStatic` to easily create enum schemas.

### Changed
- Optimised paths in error messages to prevent string duplication (e.g., removing redundant path prefixes in `SanitizrObject`, `SanitizrTuple`, and primitive schemas).

### Fixed
- Fixed bug causing doubled paths in "is required" error messages for array and object elements.

[2.0.0]: https://github.com/Nebalus/Sanitizr/compare/v1.1.1...v2.0.0


## [1.3.0] - 2026-03-18
> **Note:** This release contained breaking changes and has been retroactively re-tagged as `v2.1.0`. Use `v2.1.0` instead.

### Added
- Comprehensive string format validations mimicking Zod (e.g., `uuid`, `cuid2`, `ulid`, `ipv4`, `ipv6`, `mac`, `base64`, `emoji`, `jwt`, `hash`, `cidrv4`, `cidrv6`, etc.) to `SanitizrString`.
- Array length constraints and validations (`min`, `max`, `between`, `notEmpty`, `empty`) to `SanitizrArray`.

### Changed
- Refactored `SanitizrDiscriminatedUnion` to a generalized `SanitizrUnion` schema allowing flexible multi-schema validation without strict discriminators.
- Moved Union Schema structure into a dedicated folder.

[1.3.0]: https://github.com/Nebalus/Sanitizr/compare/v1.2.0...v1.3.0


## [1.2.0] - 2026-03-15
> **Note:** This release contained breaking changes and has been retroactively re-tagged as `v2.0.0`. Use `v2.0.0` instead.

### Added
- Zod-style structured error handling using `SanitizrError` and `SanitizrIssue` for more informative validation errors.
- `SanitizrEnum` schema for validating values against PHP enums.
- Static and instance methods `enum()` in `Sanitizr` and `SanitizrStatic` to easily create enum schemas.

### Changed
- Optimised paths in error messages to prevent string duplication (e.g., removing redundant path prefixes in `SanitizrObject`, `SanitizrTuple`, and primitive schemas).

### Fixed
- Fixed bug causing doubled paths in "is required" error messages for array and object elements.

[1.2.0]: https://github.com/Nebalus/Sanitizr/compare/v1.1.0...v1.2.0


## [1.1.1] - 2026-03-13
### Added
- `SanitizrEnum` schema for validating values against PHP enums.
- Static and instance methods `enum()` in `Sanitizr` and `SanitizrStatic` to easily create enum schemas.

[1.1.1]: https://github.com/Nebalus/Sanitizr/compare/v1.1.0...v1.1.1


## [1.1.0] - 2026-03-12
### Added
- Discriminated union schemas for type-based conditional validation.
- Phone, alphanumeric, and digit string validators.
- Post-validation transformation support.
- Separate float and integer numeric types with stringable support.
- Five comprehensive example files demonstrating validation use cases including nested objects, transformations, and complex payloads.
- Updated metadata and project configuration.

### Improved
- Enhanced numeric constraint semantics for greater clarity.
- Tuple schema (improved naming from batch).

[1.1.0]: https://github.com/Nebalus/Sanitizr/compare/v1.0.2...v1.1.0


## [1.0.2] - 2025-07-27
### Changed
- In `SafeParsedData`, the property `success` is renamed to `valid`. The method `isSuccess()` is now deprecated in favor of `isValid()`, and all logic now checks `valid` instead of `success`.
- `SanitizrNull.php` was moved from `src/Schema/Primitive/` to `src/Schema/`, with its namespace updated accordingly.
- Methods in `SanitizrNumber` and `SanitizrString` (e.g., `gt`, `gte`, `lt`, `float`, `min`, `max`, etc.) now return new cloned schema instances instead of mutating the original, improving immutability and chainability.
- Deprecated methods such as `stripTags` and `htmlSpecialChars` in `SanitizrString` are annotated as such, with references to their recommended replacements.
- Minor docblock and formatting improvements.

[1.0.2]: https://github.com/Nebalus/Sanitizr/compare/v1.0.1...v1.0.2


## [1.0.1] - 2025-07-27
### Changed
- README.md

[1.0.1]: https://github.com/Nebalus/Sanitizr/compare/v1.0.0...v1.0.1


## [1.0.0] - 2025-07-25
### Added 
- Schemas
  - Primitives Schemas
    - Boolean Schema
    - Null Schema
    - Number Schema
    - String Schema
  - Array Schema
  - Batch Schema
  - Literal Schema
  - Object Schema
- Instance based Factory (Sanitizr.php)
- Static based Factory (SanitizrStatic.php)
- SafeParse Mode
- Value Object Trait for easier schema creation for a Value Object
- Constant Based Error Messages
- Unittest Framework (~PHPUnit)

[1.0.0]: https://github.com/Nebalus/Sanitizr/releases/tag/v1.0.0
