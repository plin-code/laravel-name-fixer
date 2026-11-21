# Changelog

All notable changes to `laravel-name-fixer` will be documented in this file.

## Unreleased

### Features

- **Fixable mode for ValidName**: `new ValidName(fixable: true)` accepts any value the fixer can repair into a valid name (typographic apostrophes, doubled spaces, spaces around hyphens, trailing punctuation, invisible characters, casing), then applies the existing pattern to the fixed value
- **Locale for ValidName**: `new ValidName(locale: 'it')` runs the fixer with a locale preset, so legacy forms like `NICOLO'` are accepted. A locale implies fixable mode
- Without arguments `new ValidName` keeps validating the raw value

### Fixes

- **ValidName and line breaks**: a name ending with a line break, such as `"Rossi\n"`, was accepted. It is now rejected. In fixable mode the line break is removed by the fixer before the check

### Breaking Changes

- **SanitizeNames constructor**: the middleware now receives the `NameFixer` through its constructor. Registering it by class name works as before. Code that creates it by hand with `new SanitizeNames` must resolve it from the container or pass the fixer

### Internal

- The package no longer calls the `app()` helper. The middleware uses constructor injection, the rule, the cast and the string macros use the `NameFixer` facade

## First stable release of Laravel Name Fixer - 2026-09-24

A composable pipeline of fixers that cleans, normalizes and properly cases personal names from forms, CSV imports and legacy systems.

### Features

- **6 built-in fixers**: strip invisible characters, normalize apostrophes, strip trailing punctuation, squish whitespace, fix hyphen spacing, title case
- **Lazy title case**: names typed in mixed case (`de Rossi`) are left untouched, only all upper or all lower case names are cased
- **Italian locale preset**: turns legacy spellings like `NICOLO'` and `CANTU'` into `Nicolò` and `Cantù`, with `è` or `é` for a trailing `E'`
- **String macros**: `Str::fixName()` and `Stringable::fixName()` as a names aware alternative to `Str::title()`
- **FixedName cast**: fixes names every time they are written to a model
- **SanitizeNames middleware**: fixes name fields in incoming requests
- **ValidName validation rule**: accepts letters joined by single spaces, apostrophes or hyphens
- **Scan command**: `name-fixer:scan` finds and optionally fixes badly formatted names already in the database
- **Diagnostics**: `diagnose()` returns a FixReport with original, fixed value and applied fixers
- **Batch processing**: `fixMany()` for bulk imports
- **Standalone usage**: works outside Laravel with `NameFixer::defaults()`

### Requirements

- PHP 8.3+
- Laravel 12 or 13
