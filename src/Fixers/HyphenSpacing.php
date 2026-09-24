<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

class HyphenSpacing implements FixerInterface
{
    public function fix(string $name): string
    {
        return preg_replace('/\s*[-\x{2010}\x{2011}\x{2013}\x{2014}]\s*/u', '-', $name);
    }

    public function name(): string
    {
        return 'hyphen_spacing';
    }
}
