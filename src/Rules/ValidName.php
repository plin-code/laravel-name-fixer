<?php

namespace PlinCode\LaravelNameFixer\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use PlinCode\LaravelNameFixer\NameFixer;

class ValidName implements ValidationRule
{
    /**
     * Letters (with combining marks) joined by a single space, apostrophe or hyphen.
     * "' " is allowed as a separator for particles like "de' Medici".
     */
    private const PATTERN = "/^\p{L}\p{M}*(?:(?:[ '-]|' )?\p{L}\p{M}*)*'?$/u";

    /**
     * @param  bool  $fixable  Accept any value the fixer can repair into a valid name, not only values that are already clean.
     * @param  string|null  $locale  Locale preset applied to the fixer. A locale implies $fixable, it has no use otherwise.
     */
    public function __construct(
        private bool $fixable = false,
        private ?string $locale = null,
    ) {
        $this->fixable = $fixable || $locale !== null;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && $this->fixable) {
            $value = $this->fixer()->fix($value);
        }

        if (! is_string($value) || preg_match(self::PATTERN, $value) !== 1) {
            $fail('The :attribute field must be a valid name.')->translate();
        }
    }

    private function fixer(): NameFixer
    {
        $fixer = app(NameFixer::class);

        return $this->locale !== null ? $fixer->locale($this->locale) : $fixer;
    }
}
