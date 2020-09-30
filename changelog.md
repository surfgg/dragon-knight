# Development Changelog
All the notable changes done for Dragon Knight's development will be recorded in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
No features are currently "planned".

## v1.2.0
### Changed
- Registration now uses a [cryptographically secure method of hashing and storing passwords](https://www.php.net/manual/en/function.password-hash).
- Logging in no longer stores the user's password in the cookie, and [checks against the password hash correctly](https://www.php.net/manual/en/function.password-verify.php).
- The registration function now uses an array for errors rather than a counter and string.
- The registration function now displays the errors at the top of the register form rather than die'ing at runtime.
- The login function now displays an error at the top of the form instead of die'ing at runtime.

### Removed
- The two "patch" scripts have been removed due to having no use.

## v1.1.11
### Added
- A `mysql_` shim for PHP 7 was added in order to make the game function.

### Changed
- Updated the SQL queries in the `install.php` script so that they would run on modern MySQL servers.

## Before v1.1.11
See the [commit history for the original repo](https://github.com/renderse7en/dragon-knight/commits/master) for past changes.