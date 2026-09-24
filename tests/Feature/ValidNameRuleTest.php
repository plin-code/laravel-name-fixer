<?php

use Illuminate\Support\Facades\Validator;
use PlinCode\LaravelNameFixer\Rules\ValidName;

function passesValidName(mixed $value): bool
{
    return Validator::make(['name' => $value], ['name' => [new ValidName]])->passes();
}

it('accepts real names', function (string $name) {
    expect(passesValidName($name))->toBeTrue();
})->with([
    'Rossi',
    "D'Angelo",
    'Maria-Luisa',
    'Della Valle',
    "Giovanni de' Medici",
    'Nicolò Cantù',
    'Èrika',
    'O',
]);

it('rejects values that are not names', function (mixed $name) {
    expect(passesValidName($name))->toBeFalse();
})->with([
    'digits' => ['Rossi2'],
    'emoji' => ['Rossi 😀'],
    'at sign' => ['mario@rossi'],
    'leading hyphen' => ['-Rossi'],
    'trailing hyphen' => ['Rossi-'],
    'double space' => ['Mario  Rossi'],
    'only punctuation' => ["'-"],
    'not a string' => [['Rossi']],
]);

it('uses a translatable message', function () {
    $validator = Validator::make(['last_name' => 'R0ssi'], ['last_name' => [new ValidName]]);

    expect($validator->errors()->first('last_name'))->toBe('The last name field must be a valid name.');
});
