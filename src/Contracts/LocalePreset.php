<?php

namespace PlinCode\LaravelNameFixer\Contracts;

interface LocalePreset
{
    /** @return FixerInterface[] */
    public function fixers(): array;
}
