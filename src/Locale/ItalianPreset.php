<?php

namespace PlinCode\LaravelNameFixer\Locale;

use PlinCode\LaravelNameFixer\Contracts\LocalePreset;
use PlinCode\LaravelNameFixer\Fixers\AccentFromApostrophe;

class ItalianPreset implements LocalePreset
{
    public function __construct(
        private string $accentedE = 'è',
    ) {}

    public function fixers(): array
    {
        return [
            new AccentFromApostrophe($this->accentedE),
        ];
    }
}
