<p align="center">
  <img src="https://raw.githubusercontent.com/plin-code/laravel-name-fixer/main/art/banner.png" alt="Laravel Name Fixer">
</p>

# Laravel Name Fixer

<p align="center">
    <a href="https://packagist.org/packages/plin-code/laravel-name-fixer"><img src="https://img.shields.io/packagist/v/plin-code/laravel-name-fixer.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/plin-code/laravel-name-fixer"><img src="https://img.shields.io/packagist/php-v/plin-code/laravel-name-fixer.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/plin-code/laravel-name-fixer"><img src="https://badge.laravel.cloud/badge/plin-code/laravel-name-fixer?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/plin-code/laravel-name-fixer/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/plin-code/laravel-name-fixer/run-tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/plin-code/laravel-name-fixer"><img src="https://img.shields.io/packagist/dt/plin-code/laravel-name-fixer.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Clean, normalize and properly case personal names before validation in Laravel. Handles the mess that comes from web forms, CSV imports and legacy systems, including Italian quirks like `NICOLO'` stored instead of `Nicolò`.

## The Problem

`Str::title()` was built for titles, not for people. It turns `D'ANGELO` into `D'angelo`, keeps invisible characters pasted from a spreadsheet, and happily title cases `de Rossi` into `De Rossi` even when the user typed it that way on purpose.

**Laravel Name Fixer** repairs names once, before they reach your database, and leaves alone what it cannot know for sure.

### What It Fixes

| Input | Output | Fixer |
|-------|--------|-------|
| `Rossi\u{200B}`, `Mario\u{00A0}Rossi` | `Rossi`, `Mario Rossi` | StripInvisible |
| `D’Angelo`, ``Dall`Oglio`` | `D'Angelo`, `Dall'Oglio` | NormalizeApostrophe |
| `Rossi.`, `Rossi,` | `Rossi` | StripTrailingPunctuation |
| `  Mario   Rossi ` | `Mario Rossi` | SquishWhitespace |
| `Maria - Luisa`, `Maria – Luisa` | `Maria-Luisa` | HyphenSpacing |
| `LUCA D'ANGELO`, `sant'elia` | `Luca D'Angelo`, `Sant'Elia` | TitleCase |
| `NICOLO' CANTU'` | `Nicolò Cantù` | AccentFromApostrophe (locale: `it`) |
| `de Rossi` | `de Rossi` | left alone (mixed case) |

### What It Does Not Do

Italian surnames are registered exactly as written: `De Rossi` and `de Rossi` are both legal, and so are `Lo Presti` and `Lopresti`. From `DE ROSSI` in capitals there is no way to tell which one is right. The package title cases it to `De Rossi` and, by default, never touches a name that already comes in mixed case. If the exact spelling matters, let users correct the field.

## Installation

```bash
composer require plin-code/laravel-name-fixer
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="name-fixer-config"
```

## Quick Start

### Using the Facade

```php
use PlinCode\LaravelNameFixer\Facades\NameFixer;

NameFixer::fix("  LUCA D’ANGELO. ");
// "Luca D'Angelo"

// Get a detailed report
$report = NameFixer::diagnose('MARIO ROSSI');
// $report->original       → "MARIO ROSSI"
// $report->fixed          → "Mario Rossi"
// $report->appliedFixers  → ["title_case"]
// $report->wasModified    → true

// Batch processing
$reports = NameFixer::fixMany(['MARIO ROSSI', "d'angelo"]);
```

### Using the String Macro

A drop in replacement for `Str::title()` when the string is a person's name.

```php
use Illuminate\Support\Str;

Str::fixName("luca d'angelo");          // "Luca D'Angelo"
Str::of(' MARIO ROSSI ')->fixName();    // "Mario Rossi"
```

### Using the Cast

Fix names every time they are written to a model.

```php
use PlinCode\LaravelNameFixer\Casts\FixedName;

class Customer extends Model
{
    protected function casts(): array
    {
        return [
            'first_name' => FixedName::class,
            'last_name' => FixedName::class,
        ];
    }
}
```

### Using the Middleware

Register the `SanitizeNames` middleware to fix name fields in incoming requests before they reach validation.

```php
use PlinCode\LaravelNameFixer\Middleware\SanitizeNames;

Route::middleware(SanitizeNames::class)->group(function () {
    Route::post('/register', [RegisterController::class, 'store']);
});
```

By default the middleware targets `first_name`, `last_name`, `surname`, `nome`, `cognome`, `*_first_name` and `*_last_name`. A plain `name` field is left out on purpose, because it often holds a company or product name. You can change the list in the config file.

### Using the Validation Rule

`ValidName` accepts letters (accented ones included) joined by single spaces, apostrophes or hyphens. It rejects digits, emoji, symbols and doubled separators. It only validates: combine it with the middleware or the cast to also fix the value.

```php
use PlinCode\LaravelNameFixer\Rules\ValidName;

public function rules(): array
{
    return [
        'first_name' => ['required', 'string', 'max:100', new ValidName],
    ];
}
```

The message is `The :attribute field must be a valid name.` and can be translated through your JSON language files.

## Use Cases

### Legacy Italian Data

Old management systems and tax code records store names in uppercase ASCII: `NICOLO'`, `FORLI'`, `CANTU'`. Enable the Italian locale to turn the trailing apostrophe back into an accent.

```php
// Via config (config/name-fixer.php)
'locale' => 'it',

// Or at runtime
NameFixer::locale('it')->fix("NICOLO' CANTU'");
// "Nicolò Cantù"
```

Only a vowel at the end of a word, preceded by at least two letters, is converted. Elisions like `D'Angelo` and `Dell'Acqua` and short particles like `de' Medici` stay as they are.

A trailing `E'` becomes `è` (Mosè, Noè, Giosuè). Switch to `é` with the locale options:

```php
'locale_options' => ['accentedE' => 'é'],
```

### Cleaning Existing Records

The `name-fixer:scan` command finds badly formatted names already in your database. It only lists the changes unless you pass `--fix`.

```bash
php artisan name-fixer:scan "App\Models\Customer" --column=first_name --column=last_name --locale=it

php artisan name-fixer:scan "App\Models\Customer" --column=first_name --column=last_name --locale=it --fix
```

Rows are read in chunks (`--chunk=500` by default) and updated with the query builder, so model events, casts and `updated_at` are not touched. Run it on a backup or a staging copy first.

### CSV Imports

```php
$reports = NameFixer::fixMany(array_column($rows, 'last_name'));

foreach ($reports as $index => $report) {
    $rows[$index]['last_name'] = $report->fixed;
}
```

## Configuration

```php
return [
    // Locale preset: 'it' or null
    'locale' => null,

    // Named arguments for the locale preset
    'locale_options' => [],

    // Leave mixed case names ("de Rossi") untouched
    'lazy' => true,

    // Middleware field patterns
    'middleware' => [
        'fields' => ['first_name', 'last_name', 'surname', 'nome', 'cognome', '*_first_name', '*_last_name'],
    ],
];
```

### Custom Fixers

Build your own pipeline with any class that implements `PlinCode\LaravelNameFixer\Contracts\FixerInterface`:

```php
use PlinCode\LaravelNameFixer\Contracts\FixerInterface;
use PlinCode\LaravelNameFixer\Fixers\SquishWhitespace;
use PlinCode\LaravelNameFixer\Fixers\TitleCase;
use PlinCode\LaravelNameFixer\NameFixer;

class StripHonorifics implements FixerInterface
{
    public function fix(string $name): string
    {
        return preg_replace('/^(dott|sig|ing)\.?\s+/iu', '', $name);
    }

    public function name(): string
    {
        return 'strip_honorifics';
    }
}

$fixer = new NameFixer([
    new SquishWhitespace,
    new StripHonorifics,
    new TitleCase,
]);
```

## Standalone Usage

The fixers and the pipeline do not depend on Laravel:

```php
use PlinCode\LaravelNameFixer\NameFixer;

NameFixer::defaults()->locale('it')->fix("NICOLO' ROSSI");
// "Nicolò Rossi"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Daniele Barbaro](https://github.com/plin-code)
- [All Contributors](../../contributors)
- Inspired by [tamtamchik/namecase](https://github.com/tamtamchik/namecase)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
