<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locale Preset
    |--------------------------------------------------------------------------
    |
    | Activates locale-specific fixers. The Italian preset turns legacy
    | spellings like "NICOLO'" into "Nicolò".
    | Set to null to disable locale-specific behavior.
    |
    | Supported: 'it', null
    |
    */
    'locale' => null,

    /*
    |--------------------------------------------------------------------------
    | Locale Options
    |--------------------------------------------------------------------------
    |
    | Named arguments for the locale preset. The Italian preset accepts
    | 'accentedE', either 'è' (default) or 'é'.
    |
    */
    'locale_options' => [],

    /*
    |--------------------------------------------------------------------------
    | Lazy Casing
    |--------------------------------------------------------------------------
    |
    | When true, names typed in mixed case ("de Rossi") keep their case and
    | only all upper or all lower case names are title cased.
    |
    */
    'lazy' => true,

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Request fields fixed by the SanitizeNames middleware. Wildcards are
    | matched with Str::is(). A plain "name" field is left out on purpose,
    | it often holds a company or product name.
    |
    */
    'middleware' => [
        'fields' => ['first_name', 'last_name', 'surname', 'nome', 'cognome', '*_first_name', '*_last_name'],
    ],

];
