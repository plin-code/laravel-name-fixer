<?php

use PlinCode\LaravelNameFixer\Contracts\LocalePreset;
use PlinCode\LaravelNameFixer\Locale\ItalianPreset;

it('implements LocalePreset', function () {
    expect(new ItalianPreset)->toBeInstanceOf(LocalePreset::class);
});

it('provides the accent from apostrophe fixer', function () {
    $fixers = (new ItalianPreset)->fixers();

    expect($fixers)->toHaveCount(1)
        ->and($fixers[0]->name())->toBe('accent_from_apostrophe')
        ->and($fixers[0]->fix("MOSE'"))->toBe('MOSÈ');
});

it('forwards the accented e option', function () {
    $fixers = (new ItalianPreset(accentedE: 'é'))->fixers();

    expect($fixers[0]->fix("MOSE'"))->toBe('MOSÉ');
});
