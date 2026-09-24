<?php

namespace PlinCode\LaravelNameFixer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string fix(string $name)
 * @method static \PlinCode\LaravelNameFixer\Support\FixReport diagnose(string $name)
 * @method static \PlinCode\LaravelNameFixer\Support\FixReport[] fixMany(array $names)
 * @method static \PlinCode\LaravelNameFixer\NameFixer locale(string $locale, array $options = [])
 *
 * @see \PlinCode\LaravelNameFixer\NameFixer
 */
class NameFixer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PlinCode\LaravelNameFixer\NameFixer::class;
    }
}
