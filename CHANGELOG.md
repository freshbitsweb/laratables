# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2023-06-08
### Added
- Support for Laravel 10.x
- Support for PHP 8.2

### Changed
- Drop support EoL PHP and Laravel versions (Laravel 8.x and below, PHP 7.x and below)
- If you are using Laravel 9.x, you will need to upgrade to v9.33 ([ref](https://github.com/freshbitsweb/laratables/pull/110#pullrequestreview-1461811259))

## [2.5.0] - 2022-02-16
### Added
- Support for Laravel 9.x

## [2.4.1] - 2020-12-11
### Added
- Support for PHP 8

## [2.4.0] - 2020-09-12
### Added
- Support for Laravel 8.x

## [2.3.1] - 2020-03-21
### Fixed
- Make custom column ordering work again.

## [2.3.0] - 2020-03-04
### Added
- Support for Laravel 7.x

## [2.2.0] - 2020-02-20
### Added
- Support for Multi-column sorting [#81](https://github.com/freshbitsweb/laratables/pull/81) ([@helissonms](https://github.com/helissonms))

## [2.1.0] - 2019-09-04
### Added
- Support for Laravel 6.x

## [2.0.0] - 2019-08-03
** IMP -  ** If you're upgrading from v1.\*.\* to v2, please make the following method calls static and accept the eloquent object of the record as a parameter:
1) laratablesRowClass()
2) laratablesRowData()
3) All the methods which [customize column values](https://github.com/freshbitsweb/laratables#customizing-column-values) in your datatables.

### Fixed
- Issue with a few static methods when using custom class to specify Laratables methods [#42](https://github.com/freshbitsweb/laratables/issues/42) ([@CJau777](https://github.com/CJau777))
- Replace array destructuring shorthand with list() method to support version before PHP 7.1 [#61](https://github.com/freshbitsweb/laratables/issues/61)

## [1.1.7] - 2019-07-13
### Added
- Allow support for fetching unlimited number of records ([#43](https://github.com/freshbitsweb/laratables/issues/43))
- New config option `max_limit` to limit max number of records per query

## [1.1.5 + 1.1.6] - 2019-06-13
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