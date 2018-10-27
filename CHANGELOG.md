# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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