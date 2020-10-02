# Development Changelog
All the notable changes done for Dragon Knight's development will be recorded in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Added a new `app` directory to store libraries, vendor scripts and game config
- Created a new library for database-related functions, uses PDO in order to replace old `mysql_` functions
- Added `getControl` function that accepts a PDO link as a parameter, retrieves the control row
- Added two ways of retrieving user data; `getUserFromCookie` and `getUserFromId`
- Added a super-helpful debug method that formats var_dump and dies, called `dd()`
- Added two helper functions to increment and get the number of queries executed; `incrementQueryCounter` and `getQueryCount`
- The PDO database methods now increments the global `$queryCounter` via `incrementQueryCounter()`

### Changed
- Scripts that contain only functions have been moved into `app/Libs` and are included on an as-needed basis to cut down on root directory bloat
- Condensed the help pages into one `help.php`, moved help pages into templates in `templates/help/`
- Release versions of the game will no longer have `DEBUG` set to true by default
- Moved functions in `login.php` to `users.php`, changed all game links accordingly
- Renamed `Lib.php` to `Helpers.php`, updated previous changelog entries to reflect this
- `makesafe()` has been refactored to `safe()`, but performs the same function
- All town, fight and exploration functions now use the PDO database methods
- The `$numqueries` global variable has been refactored to `$queryCount`

### Deprecated
- The `mysql_` shim and all old functions using the `mysql_` functions are being phased out and replaced with PDO functions

### Removed
- Removed all old XML validation content. Pages are now only made with HTML5 compliant tags.

## v1.2.0 - 9/30/2020
### Changed
- The register function and installer now use a [cryptographically secure method of hashing and storing passwords](https://www.php.net/manual/en/function.password-hash).
- Logging in now [checks against the password hash correctly](https://www.php.net/manual/en/function.password-verify.php).
- The registration function now uses an array for errors rather than a counter and string.
- The registration form now uses modern HTML5 input features, and has better code indentation.
- The login function now displays an error at the top of the form instead of die'ing at runtime.
- The installer will now drop existing tables when it creates new ones.
- Cookies now store the password used at login, and checks it against the hashed version in the database.
- The installer will now set `verifyemail` to `false` by default, changed from `true`
- The `checkcookies` function was moved to `Helpers.php`

### Deprecated
- `charname` will soon be removed, and will be replaced by `username` in all instances.

### Removed
- The two "patch" scripts have been removed due to having no use.
- The registration form no longer has a `charname` field.
- The installer no longer uses a `charname` field, and instead sets `charname` to `username`
- The `cookies.php` file has been deleted, and the `checkcookies` function moved to `Helpers.php`

## v1.1.11
### Added
- A `mysql_` shim for PHP 7 was added in order to make the game function.

### Changed
- Updated the SQL queries in the `install.php` script so that they would run on modern MySQL servers.

## Before v1.1.11
See the [commit history for the original repo](https://github.com/renderse7en/dragon-knight/commits/master) for past changes.