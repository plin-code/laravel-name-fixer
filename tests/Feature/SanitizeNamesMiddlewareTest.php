<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PlinCode\LaravelNameFixer\Middleware\SanitizeNames;

function runSanitizeNames(array $input): Request
{
    $captured = null;

    (new SanitizeNames)->handle(Request::create('/', 'POST', $input), function (Request $request) use (&$captured) {
        $captured = $request;

        return new Response;
    });

    return $captured;
}

it('fixes the default name fields', function () {
    $request = runSanitizeNames([
        'first_name' => 'MARIO',
        'last_name' => "D'ANGELO",
        'nome' => 'anna',
        'cognome' => 'DELLA VALLE',
        'billing_first_name' => ' luca ',
        'billing_last_name' => 'ROSSI.',
    ]);

    expect($request->all())->toBe([
        'first_name' => 'Mario',
        'last_name' => "D'Angelo",
        'nome' => 'Anna',
        'cognome' => 'Della Valle',
        'billing_first_name' => 'Luca',
        'billing_last_name' => 'Rossi',
    ]);
});

it('does not touch other fields', function () {
    $request = runSanitizeNames(['name' => 'ACME SRL', 'city' => 'TORINO']);

    expect($request->all())->toBe(['name' => 'ACME SRL', 'city' => 'TORINO']);
});

it('uses the configured field patterns', function () {
    config()->set('name-fixer.middleware.fields', ['name']);

    $request = runSanitizeNames(['name' => 'MARIO ROSSI', 'first_name' => 'MARIO']);

    expect($request->all())->toBe(['name' => 'Mario Rossi', 'first_name' => 'MARIO']);
});

it('skips non string values', function () {
    $request = runSanitizeNames(['first_name' => ['MARIO'], 'last_name' => null]);

    expect($request->all())->toBe(['first_name' => ['MARIO'], 'last_name' => null]);
});
