<?php

use Illuminate\Support\Str;
use PlinCode\LaravelNameFixer\Facades\NameFixer as NameFixerFacade;
use PlinCode\LaravelNameFixer\NameFixer;

it('registers the fixer as a singleton', function () {
    expect(app(NameFixer::class))->toBe(app(NameFixer::class));
});

it('uses the default pipeline without a locale', function () {
    expect(app(NameFixer::class)->fix("NICOLO'"))->toBe("Nicolo'");
});

it('applies the configured locale', function () {
    config()->set('name-fixer.locale', 'it');
    app()->forgetInstance(NameFixer::class);

    expect(app(NameFixer::class)->fix("NICOLO'"))->toBe('Nicolò');
});

it('applies the configured locale options', function () {
    config()->set('name-fixer.locale', 'it');
    config()->set('name-fixer.locale_options', ['accentedE' => 'é']);
    app()->forgetInstance(NameFixer::class);

    expect(app(NameFixer::class)->fix("MOSE'"))->toBe('Mosé');
});

it('applies the configured lazy flag', function () {
    config()->set('name-fixer.lazy', false);
    app()->forgetInstance(NameFixer::class);

    expect(app(NameFixer::class)->fix('de Rossi'))->toBe('De Rossi');
});

it('exposes the fixer through the facade', function () {
    expect(NameFixerFacade::fix('MARIO ROSSI'))->toBe('Mario Rossi');
});

it('registers the Str macro', function () {
    expect(Str::fixName("luca d'angelo"))->toBe("Luca D'Angelo");
});

it('registers the Stringable macro', function () {
    expect(Str::of('  MARIO ROSSI ')->fixName()->toString())->toBe('Mario Rossi');
});
