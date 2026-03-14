# Changelog

## [2.0.6] - 2026-03-14

### Fixed
- MySQLConnection now falls back to non-SSL connection when certificate file is missing
- Removed stray MongoDB import from MySQLConnection.php
- Fixed dependency from dev-main to ^2.0 for tina4php-database

### Added
- Uniform test suite (17 tests, 21 assertions)
- Docker compose for local testing (mysql:8.0 on port 33066)


## [2.0.6] - 2026-03-14

### Added
- Uniform database driver test suite with Docker support
- phpunit.xml configuration
- GitHub Actions CI workflow
- MIT LICENSE file

### Changed
- Removed redundant classmap autoloading (PSR-4 only)
- Added PHP >= 8.1 requirement to composer.json

### Fixed
- Removed stray MongoDB import from MySQLConnection.php
