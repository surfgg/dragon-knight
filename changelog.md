# Development Changelog
All the notable changes done for Dragon Knight's development will be recorded in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Removed
- `charname` will be entirely removed, and all instances replaced with `username`.

## v1.2.0
### Changed
- Registration now uses a [cryptographically secure method of hashing and storing passwords](https://www.php.net/manual/en/function.password-hash).
- Logging in no longer stores the user's password in the cookie, and [checks against the password hash correctly](https://www.php.net/manual/en/function.password-verify.php).
- The registration function now uses an array for errors rather than a counter and string.
- The registration form now uses modern HTML5 input features, and has better code indentation.
- The login function now displays an error at the top of the form instead of die'ing at runtime.
- The installer will now drop existing tables when it creates new ones.

### Deprecated
- `charname` will soon be removed, and will be replaced by `username` in all instances.

### Removed
- The two "patch" scripts have been removed due to having no use.
- The registration form no longer has a `charname` field.
- The installer no longer uses a `charname` field, and instead sets `charname` to `username`.

## v1.1.11
### Added
- A `mysql_` shim for PHP 7 was added in order to make the game function.

### Changed
- Updated the SQL queries in the `install.php` script so that they would run on modern MySQL servers.

## Before v1.1.11
See the [commit history for the original repo](https://github.com/renderse7en/dragon-knight/commits/master) for past changes.