<?php

namespace PlinCode\LaravelNameFixer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;
use PlinCode\LaravelNameFixer\NameFixer;

class FixedName implements CastsInboundAttributes
{
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value === null ? null : app(NameFixer::class)->fix((string) $value);
    }
}
