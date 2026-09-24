<?php

namespace PlinCode\LaravelNameFixer;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PlinCode\LaravelNameFixer\Commands\FixNamesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NameFixerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('name-fixer')
            ->hasConfigFile('name-fixer')
            ->hasCommand(FixNamesCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(NameFixer::class, function ($app) {
            $config = $app['config']['name-fixer'] ?? [];

            $fixer = NameFixer::defaults(lazy: $config['lazy'] ?? true);

            if (($config['locale'] ?? null) !== null) {
                $fixer = $fixer->locale($config['locale'], $config['locale_options'] ?? []);
            }

            return $fixer;
        });
    }

    public function packageBooted(): void
    {
        Str::macro('fixName', fn (string $name): string => app(NameFixer::class)->fix($name));

        Stringable::macro('fixName', function (): Stringable {
            return new Stringable(app(NameFixer::class)->fix($this->value));
        });
    }
}
