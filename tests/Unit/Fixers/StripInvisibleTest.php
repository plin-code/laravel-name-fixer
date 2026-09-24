<?php

use PlinCode\LaravelNameFixer\Fixers\StripInvisible;

test('removes zero width characters', function () {
    expect((new StripInvisible)->fix("Ros\u{200B}si\u{FEFF}"))->toBe('Rossi');
});

test('removes soft hyphens', function () {
    expect((new StripInvisible)->fix("Ros\u{00AD}si"))->toBe('Rossi');
});

test('turns non breaking spaces into plain spaces', function () {
    expect((new StripInvisible)->fix("Mario\u{00A0}Rossi"))->toBe('Mario Rossi');
});

test('turns tabs and newlines into plain spaces', function () {
    expect((new StripInvisible)->fix("Mario\tRossi\n"))->toBe('Mario Rossi ');
});

test('keeps accented letters', function () {
    expect((new StripInvisible)->fix('Nicolò Cantù'))->toBe('Nicolò Cantù');
});

test('returns the fixer name', function () {
    expect((new StripInvisible)->name())->toBe('strip_invisible');
});
