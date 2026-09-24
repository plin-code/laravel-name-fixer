<?php

namespace PlinCode\LaravelNameFixer\Contracts;

interface FixerInterface
{
    public function fix(string $name): string;

    public function name(): string;
}
