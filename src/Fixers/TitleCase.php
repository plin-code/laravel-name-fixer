<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

class TitleCase implements FixerInterface
{
    /**
     * @param  bool  $lazy  Leave mixed case input untouched: a name typed as "de Rossi" was typed that way on purpose.
     */
    public function __construct(
        private bool $lazy = true,
    ) {}

    public function fix(string $name): string
    {
        if ($this->lazy && $this->isMixedCase($name)) {
            return $name;
        }

        return preg_replace_callback(
            "/(^|[\s'-])(\p{L})/u",
            fn (array $matches): string => $matches[1].mb_strtoupper($matches[2]),
            mb_strtolower($name),
        );
    }

    public function name(): string
    {
        return 'title_case';
    }

    private function isMixedCase(string $name): bool
    {
        return $name !== mb_strtoupper($name) && $name !== mb_strtolower($name);
    }
}
