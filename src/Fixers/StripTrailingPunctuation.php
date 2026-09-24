<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

class StripTrailingPunctuation implements FixerInterface
{
    public function fix(string $name): string
    {
        return preg_replace('/[\s.,;:]+$/u', '', $name);
    }

    public function name(): string
    {
        return 'strip_trailing_punctuation';
    }
}
