<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('contracts do not depend on Laravel')
    ->expect('PlinCode\LaravelNameFixer\Contracts')
    ->not->toUse('Illuminate');

arch('fixers do not depend on Laravel')
    ->expect('PlinCode\LaravelNameFixer\Fixers')
    ->not->toUse('Illuminate');

arch('support classes do not depend on Laravel')
    ->expect('PlinCode\LaravelNameFixer\Support')
    ->not->toUse('Illuminate');

arch('locale presets do not depend on Laravel')
    ->expect('PlinCode\LaravelNameFixer\Locale')
    ->not->toUse('Illuminate');

arch('NameFixer core does not depend on Laravel')
    ->expect('PlinCode\LaravelNameFixer\NameFixer')
    ->not->toUse('Illuminate');

arch('all fixers implement FixerInterface')
    ->expect('PlinCode\LaravelNameFixer\Fixers')
    ->toImplement('PlinCode\LaravelNameFixer\Contracts\FixerInterface');

arch('all locale presets implement LocalePreset')
    ->expect('PlinCode\LaravelNameFixer\Locale')
    ->toImplement('PlinCode\LaravelNameFixer\Contracts\LocalePreset');
