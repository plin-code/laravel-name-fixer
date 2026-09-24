<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

class SquishWhitespace implements FixerInterface
{
    public function fix(string $name): string
    {
        return trim(preg_replace('/\s+/u', ' ', $name));
    }

    public function name(): string
    {
        return 'squish_whitespace';
    }
}
