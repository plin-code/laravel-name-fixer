<?php

use PlinCode\LaravelNameFixer\Fixers\HyphenSpacing;

test('removes spaces around hyphens', function (string $input) {
    expect((new HyphenSpacing)->fix($input))->toBe('Maria-Luisa');
})->with(['Maria - Luisa', 'Maria -Luisa', 'Maria- Luisa']);

test('turns dash variants into a hyphen', function (string $input) {
    expect((new HyphenSpacing)->fix($input))->toBe('Maria-Luisa');
})->with([
    'hyphen' => ["Maria\u{2010}Luisa"],
    'non breaking hyphen' => ["Maria\u{2011}Luisa"],
    'en dash' => ["Maria \u{2013} Luisa"],
    'em dash' => ["Maria\u{2014}Luisa"],
]);

test('returns the fixer name', function () {
    expect((new HyphenSpacing)->name())->toBe('hyphen_spacing');
});
