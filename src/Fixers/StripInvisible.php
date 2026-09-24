<?php

namespace PlinCode\LaravelNameFixer\Fixers;

use PlinCode\LaravelNameFixer\Contracts\FixerInterface;

class StripInvisible implements FixerInterface
{
    public function fix(string $name): string
    {
        $name = preg_replace('/[\x{00A0}\x{2007}\x{202F}\t\r\n]/u', ' ', $name);

        return preg_replace('/[\x{00AD}\x{200B}-\x{200D}\x{2060}\x{FEFF}]|\p{Cc}/u', '', $name);
    }

    public function name(): string
    {
        return 'strip_invisible';
    }
}
