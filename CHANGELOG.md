# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)

## [Unreleased]

## [1.0.2] - 2025-07-27
### Changed
- In `SafeParsedData`, the property `success` is renamed to `valid`. The method `isSuccess()` is now deprecated in favor of `isValid()`, and all logic now checks `valid` instead of `success`.
- `SanitizrNull.php` was moved from `src/Schema/Primitive/` to `src/Schema/`, with its namespace updated accordingly.
- Methods in `SanitizrNumber` and `SanitizrString` (e.g., `gt`, `gte`, `lt`, `float`, `min`, `max`, etc.) now return new cloned schema instances instead of mutating the original, improving immutability and chainability.
- Deprecated methods such as `stripTags` and `htmlSpecialChars` in `SanitizrString` are annotated as such, with references to their recommended replacements.
- Minor docblock and formatting improvements.

[1.0.2]: https://github.com/Nebalus/Sanitizr/releases/tag/v1.0.1...v1.0.2


## [1.0.1] - 2025-07-27
### Changed
- README.md

[1.0.1]: https://github.com/Nebalus/Sanitizr/releases/tag/v1.0.0...v1.0.1


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
