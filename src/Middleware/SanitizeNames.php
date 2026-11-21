<?php

namespace PlinCode\LaravelNameFixer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PlinCode\LaravelNameFixer\NameFixer;
use Symfony\Component\HttpFoundation\Response;

class SanitizeNames
{
    public function __construct(private readonly NameFixer $fixer) {}

    public function handle(Request $request, Closure $next): Response
    {
        $fields = config('name-fixer.middleware.fields', []);
        $input = $request->all();

        foreach ($input as $key => $value) {
            if (is_string($value) && Str::is($fields, $key)) {
                $input[$key] = $this->fixer->fix($value);
            }
        }

        $request->merge($input);

        return $next($request);
    }
}
