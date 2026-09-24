<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CommandPerson extends Model
{
    protected $table = 'people';

    protected $guarded = [];
}

beforeEach(function () {
    Schema::create('people', function (Blueprint $table) {
        $table->id();
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->timestamps();
    });

    DB::table('people')->insert([
        ['first_name' => 'MARIO', 'last_name' => 'ROSSI', 'updated_at' => '2020-01-01 00:00:00'],
        ['first_name' => 'Anna', 'last_name' => 'de Rossi', 'updated_at' => '2020-01-01 00:00:00'],
        ['first_name' => null, 'last_name' => "d'angelo", 'updated_at' => '2020-01-01 00:00:00'],
    ]);
});

it('lists the changes without saving them by default', function () {
    $this->artisan('name-fixer:scan', ['model' => CommandPerson::class, '--column' => ['first_name', 'last_name']])
        ->expectsOutputToContain('3 changes found. Run again with --fix to save them.')
        ->assertSuccessful();

    expect(DB::table('people')->pluck('last_name')->all())->toBe(['ROSSI', 'de Rossi', "d'angelo"]);
});

it('saves the changes with --fix without touching timestamps', function () {
    $this->artisan('name-fixer:scan', ['model' => CommandPerson::class, '--column' => ['first_name', 'last_name'], '--fix' => true])
        ->expectsOutputToContain('3 changes saved')
        ->assertSuccessful();

    expect(DB::table('people')->pluck('first_name')->all())->toBe(['Mario', 'Anna', null])
        ->and(DB::table('people')->pluck('last_name')->all())->toBe(['Rossi', 'de Rossi', "D'Angelo"])
        ->and(DB::table('people')->pluck('updated_at')->unique()->all())->toBe(['2020-01-01 00:00:00']);
});

it('applies the locale option', function () {
    DB::table('people')->insert(['first_name' => "NICOLO'", 'last_name' => 'BIANCHI']);

    $this->artisan('name-fixer:scan', ['model' => CommandPerson::class, '--column' => ['first_name'], '--locale' => 'it', '--fix' => true])
        ->assertSuccessful();

    expect(DB::table('people')->where('last_name', 'BIANCHI')->value('first_name'))->toBe('Nicolò');
});

it('reports when nothing needs fixing', function () {
    $this->artisan('name-fixer:scan', ['model' => CommandPerson::class, '--column' => ['id']])
        ->expectsOutputToContain('No names to fix.')
        ->assertSuccessful();
});

it('fails without columns', function () {
    $this->artisan('name-fixer:scan', ['model' => CommandPerson::class])
        ->expectsOutputToContain('Pass at least one --column.')
        ->assertFailed();
});

it('fails on a class that is not a model', function () {
    $this->artisan('name-fixer:scan', ['model' => stdClass::class, '--column' => ['last_name']])
        ->expectsOutputToContain('stdClass is not an Eloquent model.')
        ->assertFailed();
});
