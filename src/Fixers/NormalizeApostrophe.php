<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

class NormalizeApostrophe implements FixerInterface
{
    public function fix(string $name): string
    {
        return preg_replace('/[\x{2019}\x{2018}\x{00B4}`\x{02BC}\x{2032}]/u', "'", $name);
    }

    public function name(): string
    {
        return 'normalize_apostrophe';
    }
}
