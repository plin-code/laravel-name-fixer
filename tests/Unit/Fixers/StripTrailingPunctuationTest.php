<?php

use PlinCode\LaravelNameFixer\Fixers\StripTrailingPunctuation;

test('strips trailing punctuation', function (string $input) {
    expect((new StripTrailingPunctuation)->fix($input))->toBe('Rossi');
})->with(['Rossi.', 'Rossi,', 'Rossi;', 'Rossi:', 'Rossi.,', 'Rossi. ']);

test('keeps a trailing apostrophe', function () {
    expect((new StripTrailingPunctuation)->fix("NICOLO'"))->toBe("NICOLO'");
});

test('keeps inner punctuation', function () {
    expect((new StripTrailingPunctuation)->fix("Dell'Acqua"))->toBe("Dell'Acqua");
});

test('returns the fixer name', function () {
    expect((new StripTrailingPunctuation)->name())->toBe('strip_trailing_punctuation');
});
