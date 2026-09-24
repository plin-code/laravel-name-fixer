<?php

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;
use PlinCode\LaravelNameFixer\NameFixer;
use PlinCode\LaravelNameFixer\Support\FixReport;

it('runs the default pipeline', function () {
    expect(NameFixer::defaults()->fix("  LUCA   D\u{2019}ANGELO. "))->toBe("Luca D'Angelo");
});

it('returns empty string for empty input', function () {
    expect(NameFixer::defaults()->fix(''))->toBe('');
});

it('does not touch clean names', function (string $name) {
    expect(NameFixer::defaults()->fix($name))->toBe($name);
})->with(['Mario Rossi', "Luca D'Angelo", 'de Rossi', "Giovanni de' Medici", 'Maria-Luisa Dell\'Acqua']);

it('does not convert apostrophes into accents without the italian locale', function () {
    expect(NameFixer::defaults()->fix("NICOLO'"))->toBe("Nicolo'");
});

it('can title case mixed case input when not lazy', function () {
    expect(NameFixer::defaults(lazy: false)->fix('de Rossi'))->toBe('De Rossi');
});

it('returns a report from diagnose', function () {
    $report = NameFixer::defaults()->diagnose('  MARIO ROSSI ');

    expect($report)->toBeInstanceOf(FixReport::class)
        ->and($report->original)->toBe('  MARIO ROSSI ')
        ->and($report->fixed)->toBe('Mario Rossi')
        ->and($report->wasModified)->toBeTrue()
        ->and($report->appliedFixers)->toBe(['strip_trailing_punctuation', 'squish_whitespace', 'title_case']);
});

it('reports clean names as unmodified', function () {
    $report = NameFixer::defaults()->diagnose('Mario Rossi');

    expect($report->wasModified)->toBeFalse()
        ->and($report->appliedFixers)->toBeEmpty();
});

it('batch processes names via fixMany', function () {
    $reports = NameFixer::defaults()->fixMany(['MARIO ROSSI', 'anna bianchi']);

    expect($reports)->toHaveCount(2)
        ->and($reports[0]->fixed)->toBe('Mario Rossi')
        ->and($reports[1]->fixed)->toBe('Anna Bianchi');
});

it('accepts a custom pipeline', function () {
    $upper = new class implements FixerInterface
    {
        public function fix(string $name): string
        {
            return mb_strtoupper($name);
        }

        public function name(): string
        {
            return 'upper';
        }
    };

    expect((new NameFixer([$upper]))->fix('rossi'))->toBe('ROSSI');
});

it('adds the italian fixers with the italian locale', function () {
    expect(NameFixer::defaults()->locale('it')->fix("NICOLO' CANTU'"))->toBe('Nicolò Cantù');
});

it('runs locale fixers before title case', function () {
    $fixer = NameFixer::defaults()->locale('it');

    expect($fixer->diagnose("NICOLO'")->appliedFixers)->toBe(['accent_from_apostrophe', 'title_case']);
});

it('appends locale fixers when the pipeline has no title case', function () {
    expect((new NameFixer([]))->locale('it')->fix("NICOLO'"))->toBe('NICOLÒ');
});

it('passes locale options to the preset', function () {
    expect(NameFixer::defaults()->locale('it', ['accentedE' => 'é'])->fix("MOSE'"))->toBe('Mosé');
});

it('returns a new instance from locale', function () {
    $original = NameFixer::defaults();
    $italian = $original->locale('it');

    expect($italian)->not->toBe($original)
        ->and($original->fix("NICOLO'"))->toBe("Nicolo'");
});

it('throws on unknown locale', function () {
    NameFixer::defaults()->locale('xx');
})->throws(InvalidArgumentException::class, 'Unknown locale preset: xx');

it('keeps title case as the last default fixer', function () {
    $report = NameFixer::defaults()->diagnose('a');

    expect($report->appliedFixers)->toBe(['title_case']);
});

it('passes all fixture cases', function () {
    $fixtures = json_decode(file_get_contents(__DIR__.'/../fixtures/names.json'), true);

    foreach ($fixtures as $i => $case) {
        $fixer = NameFixer::defaults();

        if ($case['locale'] !== null) {
            $fixer = $fixer->locale($case['locale']);
        }

        $result = $fixer->fix($case['input']);

        expect($result)->toBe(
            $case['expected'],
            "Fixture #{$i}: '{$case['input']}' expected '{$case['expected']}', got '{$result}'",
        );
    }
});
