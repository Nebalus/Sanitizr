# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)

## [Unreleased]


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
