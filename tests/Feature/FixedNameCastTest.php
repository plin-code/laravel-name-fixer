<?php

use Illuminate\Database\Eloquent\Model;
use PlinCode\LaravelNameFixer\Casts\FixedName;

class CastPerson extends Model
{
    protected $guarded = [];

    protected $casts = ['last_name' => FixedName::class];
}

it('fixes the value when it is set', function () {
    $person = new CastPerson(['last_name' => "  D'ANGELO "]);

    expect($person->getAttributes()['last_name'])->toBe("D'Angelo");
});

it('keeps null values', function () {
    $person = new CastPerson(['last_name' => null]);

    expect($person->getAttributes()['last_name'])->toBeNull();
});
