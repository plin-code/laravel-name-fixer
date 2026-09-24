<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use InvalidArgumentException;
use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

/**
 * Legacy Italian systems store accented letters as vowel plus apostrophe ("NICOLO'", "CANTU'").
 * Only a word final vowel preceded by at least two letters is converted, so elisions like
 * "D'Angelo" and short particles like "de' Medici" stay as they are.
 */
class AccentFromApostrophe implements FixerInterface
{
    /** @var array<string, string> */
    private array $accents;

    public function __construct(string $accentedE = 'è')
    {
        if (! in_array($accentedE, ['è', 'é'], true)) {
            throw new InvalidArgumentException("accentedE must be \"è\" or \"é\", got \"{$accentedE}\"");
        }

        $this->accents = ['a' => 'à', 'e' => $accentedE, 'i' => 'ì', 'o' => 'ò', 'u' => 'ù'];
    }

    public function fix(string $name): string
    {
        return preg_replace_callback(
            "/(?<=\p{L}{2})([aeiou])'(?=[\s-]|$)/iu",
            fn (array $matches): string => $this->accent($matches[1]),
            $name,
        );
    }

    public function name(): string
    {
        return 'accent_from_apostrophe';
    }

    private function accent(string $vowel): string
    {
        $accented = $this->accents[strtolower($vowel)];

        return ctype_upper($vowel) ? mb_strtoupper($accented) : $accented;
    }
}
