# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2021-12-04
### Added
 - support for **Symfony** v6
 - **PHP CodeSniffer**, **PHP Compatibility**, **PHPCPD**, **PHPStan** & **PHPStan strict rules** to the CI

## [2.0.0] - 2021-11-14
### Changed
 - minimal **league/commonmark** supported version is v2.0
 - minimal **PHP** supported version is 7.4

### Removed
 - converter service IDs as `aymdev_commonmark.converter.CONVERTER_NAME`

## [1.3.1] - 2020-11-08
### Fixed
 - definition deprecation for *old* service IDs has been corrected for Symfony < 5.1

## [1.3.0] - 2020-11-07
### Added
 - converter names as services IDs

### Deprecated
 - converter service IDs as `aymdev_commonmark.converter.CONVERTER_NAME` 

### Changed
 - Replace Twig extension constructor arguments with a service locator to improve performances

## [1.2.0] - 2020-10-25
### Added
 - New `empty` converter type

## [1.1.1] - 2020-10-24
### Fixed
 - the converter `options` configuration key is not ignored anymore

## [1.1.0] - 2020-10-23
### Added
 - **Twig** `commonmark` filter

## [1.0.0] - 2020-10-16
### Added
 - Bundle base

[Unreleased]: https://github.com/AymDev/CommonMarkBundle/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/AymDev/CommonMarkBundle/compare/v1.3.1...v2.0.0
[1.3.1]: https://github.com/AymDev/CommonMarkBundle/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/AymDev/CommonMarkBundle/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/AymDev/CommonMarkBundle/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/AymDev/CommonMarkBundle/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/AymDev/CommonMarkBundle/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/AymDev/CommonMarkBundle/releases/tag/v1.0.0

