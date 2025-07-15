# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)

## [Unreleased]

## [2.0.1] - 2025-07-15
### Bug Fixes
- Improved detection and handling of default values and missing properties in schemas, ensuring more accurate validation and assignment of defaults.
- Enhanced boolean value parsing to consistently interpret input values, regardless of type.
- Refined presence checks for object properties to correctly recognize keys with null values.

### Documentation
- Clarified method comments regarding optional fields and default value behavior.

### Refactor
- Updated the namespace of a trait for consistency.

[2.0.1]: https://github.com/Nebalus/Sanitizr/releases/tag/v2.0.1


## [2.0.0] - 2025-07-08
### Added 
- Unittest Framework (~PHPUnit)
- Batch Schema
- Null Schema

[2.0.0]: https://github.com/Nebalus/Sanitizr/releases/tag/v2.0.0


## [1.0.1] - 2025-01-15
### Added
- Added PHP version requirements to composer.json

[1.0.1]: https://github.com/Nebalus/Sanitizr/releases/tag/v1.0.1


## [1.0.0] - 2025-01-15
### Added 
- Added base structures
- Added some Validation Schemas

[1.0.0]: https://github.com/Nebalus/Sanitizr/releases/tag/v1.0.0
