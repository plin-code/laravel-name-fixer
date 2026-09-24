<?php

namespace PlinCode\LaravelNameFixer\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidName implements ValidationRule
{
    /**
     * Letters (with combining marks) joined by a single space, apostrophe or hyphen.
     * "' " is allowed as a separator for particles like "de' Medici".
     */
    private const PATTERN = "/^\p{L}\p{M}*(?:(?:[ '-]|' )?\p{L}\p{M}*)*'?$/u";

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || preg_match(self::PATTERN, $value) !== 1) {
            $fail('The :attribute field must be a valid name.')->translate();
        }
    }
}
