<?php

use PlinCode\LaravelNameFixer\Fixers\TitleCase;

test('title cases upper and lower case names', function (string $input, string $expected) {
    expect((new TitleCase)->fix($input))->toBe($expected);
})->with([
    ['MARIO DI MAIO', 'Mario Di Maio'],
    ['anna della valle', 'Anna Della Valle'],
    ["LUCA D'ANGELO", "Luca D'Angelo"],
    ["sant'elia", "Sant'Elia"],
    ['MARIA-LUISA', 'Maria-Luisa'],
    ['ÈRIKA LO PRESTI', 'Èrika Lo Presti'],
    ['NICOLÒ CANTÙ', 'Nicolò Cantù'],
]);

test('leaves mixed case names alone by default', function (string $input) {
    expect((new TitleCase)->fix($input))->toBe($input);
})->with(['de Rossi', "Giovanni de' Medici", 'McDonald', 'Lo presti']);

test('title cases mixed case names when not lazy', function () {
    expect((new TitleCase(lazy: false))->fix('de Rossi'))->toBe('De Rossi');
});

test('leaves strings without letters alone', function () {
    expect((new TitleCase)->fix("' -"))->toBe("' -");
});

test('returns the fixer name', function () {
    expect((new TitleCase)->name())->toBe('title_case');
});
