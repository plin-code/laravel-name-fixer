<?php

use Illuminate\Support\Facades\Validator;
use PlinCode\LaravelNameFixer\NameFixer;
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

function passesFixableName(mixed $value, ?string $locale = null): bool
{
    return Validator::make(['name' => $value], ['name' => [new ValidName(fixable: true, locale: $locale)]])->passes();
}

it('keeps rejecting unclean names by default', function (string $name) {
    expect(passesValidName($name))->toBeFalse();
})->with([
    'typographic apostrophe' => ['D’Angelo'],
    'spaces around hyphen' => ['Maria - Luisa'],
    'trailing dot' => ['Rossi.'],
    'trailing comma' => ['Rossi,'],
    'zero width space' => ["Ros\u{200B}si"],
]);

it('accepts in fixable mode what the fixer can repair', function (string $name, ?string $locale) {
    expect(passesFixableName($name, $locale))->toBeTrue();
})->with([
    'typographic apostrophe' => ['D’Angelo', null],
    'grave accent as apostrophe' => ['Dall`Oglio', null],
    'doubled spaces' => ['Mario   Rossi', null],
    'surrounding spaces' => ['  Mario Rossi  ', null],
    'spaces around hyphen' => ['Maria - Luisa', null],
    'en dash around words' => ['Maria – Luisa', null],
    'trailing dot' => ['Rossi.', null],
    'trailing comma' => ['Rossi,', null],
    'all caps' => ['MARIO ROSSI', null],
    'lower case' => ['mario rossi', null],
    'zero width space' => ["Ros\u{200B}si", null],
    'soft hyphen' => ["Ros\u{00AD}si", null],
    'non breaking space' => ["Mario\u{00A0}Rossi", null],
    'newline as separator' => ["Mario\nRossi", null],
    'legacy italian form' => ["NICOLO'", 'it'],
    'legacy italian form with typographic apostrophe' => ['NICOLO’ CANTU’.', 'it'],
]);

it('rejects in fixable mode what the fixer cannot repair', function (mixed $name) {
    expect(passesFixableName($name))->toBeFalse();
})->with([
    'digits' => ['Agenzia 2'],
    'markdown link' => ['[x](https://evil.example)'],
    'html tag' => ['<a href=x>'],
    'domain' => ['evil.example'],
    'url' => ['https://evil.example'],
    'forward slash' => ['Mario/Rossi'],
    'backslash' => ['Mario\\Rossi'],
    'at sign' => ['mario@rossi'],
    'emoji' => ['Rossi 😀'],
    'symbol' => ['Rossi & Figli'],
    'only punctuation' => ['...'],
    'only invisible characters' => ["\u{200B}\u{200B}"],
    'trailing hyphen' => ['Rossi -'],
    'leading hyphen' => ['- Rossi'],
    'leading apostrophe' => ["'Rossi"],
    'dot inside' => ['Mario.Rossi'],
    'array' => [['Rossi']],
    'integer' => [42],
    'boolean' => [true],
]);

// Laravel skips non implicit rules for empty values, so the rule is called directly.
it('rejects empty values in fixable mode', function (mixed $name) {
    $failed = false;

    (new ValidName(fixable: true))->validate('name', $name, function () use (&$failed) {
        $failed = true;

        return new class
        {
            public function translate(): void {}
        };
    });

    expect($failed)->toBeTrue();
})->with(['empty string' => [''], 'only whitespace' => ['   '], 'null' => [null]]);

it('still accepts clean names in fixable mode', function (string $name) {
    expect(passesFixableName($name))->toBeTrue();
})->with(['Rossi', "D'Angelo", 'Maria-Luisa', "Giovanni de' Medici", 'Nicolò Cantù']);

it('accepts every fixture input in fixable mode and every fixed output in default mode', function () {
    $fixtures = json_decode(file_get_contents(__DIR__.'/../fixtures/names.json'), true);

    foreach ($fixtures as $i => $case) {
        expect(passesFixableName($case['input'], $case['locale']))->toBeTrue("Fixture #{$i} input");
        expect(passesValidName($case['expected']))->toBeTrue("Fixture #{$i} expected");
    }
});

it('does not change the validated data', function () {
    $validator = Validator::make(['name' => 'MARIO  ROSSI.'], ['name' => [new ValidName(fixable: true)]]);

    expect($validator->passes())->toBeTrue()
        ->and($validator->getData()['name'])->toBe('MARIO  ROSSI.');
});

it('implies fixable mode when a locale is given', function () {
    expect(Validator::make(['name' => "NICOLO'."], ['name' => [new ValidName(locale: 'it')]])->passes())->toBeTrue();
});

it('uses the configured locale of the fixer', function () {
    config(['name-fixer.locale' => 'it']);
    app()->forgetInstance(NameFixer::class);

    expect(passesFixableName("NICOLO'"))->toBeTrue();
});

it('throws on an unknown locale', function () {
    passesFixableName('Rossi', 'xx');
})->throws(InvalidArgumentException::class, 'Unknown locale preset: xx');

it('uses the same translatable message in fixable mode', function () {
    $validator = Validator::make(['last_name' => 'R0ssi'], ['last_name' => [new ValidName(fixable: true)]]);

    expect($validator->errors()->first('last_name'))->toBe('The last name field must be a valid name.');
});
