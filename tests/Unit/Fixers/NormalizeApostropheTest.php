<?php

use PlinCode\LaravelNameFixer\Fixers\NormalizeApostrophe;

test('normalizes apostrophe variants', function (string $input) {
    expect((new NormalizeApostrophe)->fix($input))->toBe("D'Angelo");
})->with([
    'right single quote' => ["D\u{2019}Angelo"],
    'left single quote' => ["D\u{2018}Angelo"],
    'acute accent' => ["D\u{00B4}Angelo"],
    'grave accent' => ['D`Angelo'],
    'modifier letter' => ["D\u{02BC}Angelo"],
    'prime' => ["D\u{2032}Angelo"],
]);

test('leaves plain apostrophes alone', function () {
    expect((new NormalizeApostrophe)->fix("Dell'Acqua"))->toBe("Dell'Acqua");
});

test('returns the fixer name', function () {
    expect((new NormalizeApostrophe)->name())->toBe('normalize_apostrophe');
});
