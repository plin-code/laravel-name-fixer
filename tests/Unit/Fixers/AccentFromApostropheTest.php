<?php

use PlinCode\LaravelNameFixer\Fixers\AccentFromApostrophe;

test('turns a final vowel and apostrophe into an accented vowel', function (string $input, string $expected) {
    expect((new AccentFromApostrophe)->fix($input))->toBe($expected);
})->with([
    ["NICOLO'", 'NICOLÒ'],
    ["CANTU'", 'CANTÙ'],
    ["FORLI'", 'FORLÌ'],
    ["MOSE'", 'MOSÈ'],
    ["GIOSUE' ROSSI", 'GIOSUÈ ROSSI'],
    ["nicolo'", 'nicolò'],
    ["NICOLO' ROSSI", 'NICOLÒ ROSSI'],
    ["DE NICOLO'-BIANCHI", 'DE NICOLÒ-BIANCHI'],
]);

test('uses the acute e when asked', function () {
    expect((new AccentFromApostrophe(accentedE: 'é'))->fix("MOSE'"))->toBe('MOSÉ');
});

test('rejects an accented e that is not è or é', function () {
    new AccentFromApostrophe(accentedE: 'e');
})->throws(InvalidArgumentException::class, 'accentedE must be "è" or "é", got "e"');

test('leaves elisions alone', function (string $input) {
    expect((new AccentFromApostrophe)->fix($input))->toBe($input);
})->with(["D'ANGELO", "DELL'ACQUA", "SANT'ELIA", "Giovanni de' Medici", "PO'"]);

test('returns the fixer name', function () {
    expect((new AccentFromApostrophe)->name())->toBe('accent_from_apostrophe');
});
