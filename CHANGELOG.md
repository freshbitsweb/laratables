# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.5] - 2019-06-13
### Fixed
- Update method name as to get column for `MorphTo` relationships

## [1.1.4] - 2019-02-28
### Added
- Support for Laravel 5.8

## [1.1.3] - 2019-01-10
### Added
- Allow order by raw statements
- CONTRIBUTING file
- .gitattributes file

### Fixed
- Internal: Initialize search and select columns as an array

## [1.1.2] - 2018-11-30
### Added
- Allow specifying non-searchable columns in config file #26 (@sharifzadesina)

## [1.1.1] - 2018-10-27
### Added
- Tests
- Separate searchable columns #21 (@mirabdolbaghi)
- Separate class for `laratables` methods #7 (@shadoWalker89)

## [1.1.0] - 2018-10-06
### Added
- A feature to modify the query with `recordsOf()` method (@cvsouth)
- Note about the non-support of nested relationships.

## [1.0.9] - 2018-09-22
### Added
- Changelog file
- Link to online demo of the package - https://laratables.freshbits.in.

### Fixed
- Additional check for Carbon\Carbon instance in RecordsTransformer #10 (@felipesp88)
- Parse limit and offset values as integers #9 (@felipesp88)
- Proper doc blocks #11 (@cvsouth)