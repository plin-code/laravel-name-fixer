# Changelog

All notable changes to `laravel-name-fixer` will be documented in this file.

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
