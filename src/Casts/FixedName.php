<?php

namespace PlinCode\LaravelNameFixer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;
use PlinCode\LaravelNameFixer\Facades\NameFixer;

class FixedName implements CastsInboundAttributes
{
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value === null ? null : NameFixer::fix((string) $value);
    }
}
