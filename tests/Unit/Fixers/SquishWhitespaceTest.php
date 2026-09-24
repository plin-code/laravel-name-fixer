<?php

use PlinCode\LaravelNameFixer\Fixers\SquishWhitespace;

test('collapses inner whitespace and trims the ends', function () {
    expect((new SquishWhitespace)->fix('  Mario   Rossi  '))->toBe('Mario Rossi');
});

test('leaves clean names alone', function () {
    expect((new SquishWhitespace)->fix('Mario Rossi'))->toBe('Mario Rossi');
});

test('returns the fixer name', function () {
    expect((new SquishWhitespace)->name())->toBe('squish_whitespace');
});
