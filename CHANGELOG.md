# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

Nothing yet

## [2.2.3] - 2022-12-17

### Changed

- Fixed return type for PHP 8.1 compatibility
  ([#23](https://github.com/scriptotek/php-marc/pull/23), by [@gerricom](https://github.com/gerricom))
- Add PHP 8.2 to test matrix

## [2.2.1] - 2021-05-06

### Changed

- Fixed crash when 250 is missing
  ([6850aed](https://github.com/scriptotek/php-marc/commit/6850aedf9a3a41f49fd026336283c487e844919c))

### Changed

- Made `getSubfieldValues()` public.
  ([#21](https://github.com/scriptotek/php-marc/pull/21), by [@rudolfbyker](https://github.com/rudolfbyker))

## [2.2.0] - 2020-09-16

### Added

- Added edition property to BibliographicRecord.
  ([da949e6](https://github.com/scriptotek/php-marc/commit/da949e640e86be7498f26d0e74fbb6c26bfcbce3))

### Changed

- Made `getSubfieldValues()` public.
  ([#21](https://github.com/scriptotek/php-marc/pull/21), by [@rudolfbyker](https://github.com/rudolfbyker))

### Fixed

- Fixed return types in @method annotations.
  ([#20](https://github.com/scriptotek/php-marc/pull/20), by [@rudolfbyker](https://github.com/rudolfbyker))

## [2.1.0] - 2019-11-20

### Added

- Added info to contributors (CONTRIBUTING.md).
  ([62949a1](https://github.com/scriptotek/php-marc/commit/62949a1b2e1c309e3bf8bb58f9f8f138c0398d46))
- Added initialization from SimpleXMLElement object through the new methods
  `Collection:fromSimpleXMLElement($obj)` and `Record:fromSimpleXMLElement($obj)`.

### Fixed

- Improved documentation and support for IDE code analysis.
  ([#15](https://github.com/scriptotek/php-marc/issues/15)
  by [@rudolfbyker](https://github.com/rudolfbyker))

## [2.0.2] - 2019-09-13

### Added

- Added new method `Field::asLineMarc()` to return a line mode Marc string
  representation of the field.
  ([ba20a6d](https://github.com/scriptotek/php-marc/commit/ba20a6deadc9402bb65807cd63e33797d2893dea))

### Fixed

- Fixed the `Subject::getParts()` method.
  ([1fe8408](https://github.com/scriptotek/php-marc/commit/1fe8408e49c6c3afba9ec379b441c82f64ce0336))
- Added additional subject subfield codes that were missing.
  ([7908616](https://github.com/scriptotek/php-marc/commit/79086165dfce9b9d2f490d38e9f50f70fef5641f))
- Added 852 $i and $j to `Location.callCode`.
  ([cba1508](https://github.com/scriptotek/php-marc/commit/cba15083422bb2ac812b6b355341feab2cff308a))
- Fixed the string representation of the `Location` class.
  ([74652a3](https://github.com/scriptotek/php-marc/commit/74652a3bf4cc3e9fe3c916057a0a9bd47419f601))

## [2.0.1] - 2019-01-09

### Fixed

- Fixed strict comparison in `Field::mapSubFields()` to avoid matching `0`
  to other subfields.

## [2.0.0] - 2018-10-23

### Added

- Added new helper methods to `HoldingsRecord`: `getLocation()` and `getLocations()` for 852 fields.
- Added new helper methods to `BibliographicRecord`:
  - `getCreators()` for 100 and 700 fields.
  - `getClassifications()` for 080, 082, 083, 084 fields.
  - `getPublisher()` for 26[04]$b
  - `getPubYear()` for pub year in 008
  - `getToc()` for 505 fields
  - `getSummary()` for 520 fields
  - `getPartOf()` for 773 fields
- Added a `mapSubFields()` method to the `Field` class.
- Made the `Record` class JSON serializable.
- Added a `getType()` and `getTag()` method to `Classification`.

### Changed

- Changed the `Field::sf()` method to return `NULL`, not an empty string,
  when no matching subfield was found.
- Changed `Record::query()`, `Record::getField()` etc. to return `Field`
  objects rather than raw File_MARC objects.
- Split the `Record` class into classes that reflect the type of
  record (`HoldingsRecord`, `AuthorityRecord` and `BibliographicRecord`)
  and inherit from the `Record` class.
- Renamed `Subject::getControlNumber()` to `Subject::getId()`.
- Added chopping of ending punctuation from the string representations of
  `Subject` and `Person` in the same way as done by Library of Congress
  when they convert MARC21 to MODS and BibFrame
  (see discussion on ISBD punctuation in [MARC DISCUSSION PAPER NO. 2010-DP01](https://www.loc.gov/marc/marbi/2010/2010-dp01.html)).

## [1.0.1] - 2017-12-04
### Fixed

- Fixed a bug in `QueryResult::count()`.

## [1.0.0] - 2017-07-02
### Changed

- Removed support for PHP 5.5, now requires PHP 5.6 or 7.x

## [0.3.2] - 2017-01-15

### Changed

- Added `JsonSerializable` implementations to the `Field` classes to make them behave better when passed through `json_encode()`.
- Officially removed PHP 5.4 support
- Re-licensed as MIT (But since the dependency File_MARC is licensed under LGPL-2.1, the library cannot be used without complying with LGPL-2.1).

## [0.3.1] - 2017-01-15
### Fixed

- Fixed a bug where `makeFieldObjects()` would not create the correct class.

## [0.3.0] - 2016-11-19

### Changed
- `Record::get()` was replaced by `Record::query()`, which returns a `QueryResult` object rather than an array of strings.
  This allows access to the marc fields / subfields matched by the query.
- `Collection::records` has been removed in favor of making the records available directly on the `Collection` class.
  Replace `foreach ($collection->records as $record)` with `foreach ($collection as $record)`.
- `Subject::getType()` now returns the tag number (like "650)" instead of a string representing the tag (like "topic").
  Constants have been defined on `Subject` for comparison, so to check if a subject is a topical term,
  you can do `$subject->type == Subject::TOPICAL_TERM`.
- `Record::fromString` now throws a `RecordNotFound` exception rather than an `ErrorException` exception if no record was found.
- `Record::getType` now throws a `UnknownRecordType` exception rather than an `ErrorException`.

[Unreleased]: https://github.com/scriptotek/php-marc/compare/v2.1.0...HEAD
[2.1.0]: https://github.com/scriptotek/php-marc/compare/v2.0.2...v2.1.0
[2.0.2]: https://github.com/scriptotek/php-marc/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/scriptotek/php-marc/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/scriptotek/php-marc/compare/v1.0.1...v2.0.0
[1.0.1]: https://github.com/scriptotek/php-marc/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/scriptotek/php-marc/compare/v0.3.2...v1.0.0
[0.3.2]: https://github.com/scriptotek/php-marc/compare/v0.3.1...v0.3.2
[0.3.1]: https://github.com/scriptotek/php-marc/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/scriptotek/php-marc/compare/v0.2.1...v0.3.0
