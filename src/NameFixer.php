<?php

namespace PlinCode\LaravelNameFixer;

use InvalidArgumentException;
use PlinCode\LaravelNameFixer\Contracts\FixerInterface;
use PlinCode\LaravelNameFixer\Contracts\LocalePreset;
use PlinCode\LaravelNameFixer\Fixers\HyphenSpacing;
use PlinCode\LaravelNameFixer\Fixers\NormalizeApostrophe;
use PlinCode\LaravelNameFixer\Fixers\SquishWhitespace;
use PlinCode\LaravelNameFixer\Fixers\StripInvisible;
use PlinCode\LaravelNameFixer\Fixers\StripTrailingPunctuation;
use PlinCode\LaravelNameFixer\Fixers\TitleCase;
use PlinCode\LaravelNameFixer\Locale\ItalianPreset;
use PlinCode\LaravelNameFixer\Support\FixReport;

final class NameFixer
{
    /** @var array<string, class-string<LocalePreset>> */
    private static array $presets = [
        'it' => ItalianPreset::class,
    ];

    /**
     * @param  FixerInterface[]  $fixers
     */
    public function __construct(
        private array $fixers,
    ) {}

    public function fix(string $name): string
    {
        return $this->diagnose($name)->fixed;
    }

    public function diagnose(string $name): FixReport
    {
        $fixed = $name;
        $appliedFixers = [];

        foreach ($this->fixers as $fixer) {
            $result = $fixer->fix($fixed);

            if ($result !== $fixed) {
                $appliedFixers[] = $fixer->name();
                $fixed = $result;
            }
        }

        return new FixReport(
            original: $name,
            fixed: $fixed,
            appliedFixers: $appliedFixers,
            wasModified: $name !== $fixed,
        );
    }

    /**
     * @param  string[]  $names
     * @return FixReport[]
     */
    public function fixMany(array $names): array
    {
        return array_map(fn (string $name): FixReport => $this->diagnose($name), $names);
    }

    /**
     * @param  array<string, mixed>  $options  Named arguments for the preset constructor.
     */
    public function locale(string $locale, array $options = []): self
    {
        $presetClass = self::$presets[$locale]
            ?? throw new InvalidArgumentException("Unknown locale preset: {$locale}");

        $preset = new $presetClass(...$options);

        // Locale fixers repair characters, so they must run before the case is decided.
        $insertIndex = count($this->fixers);

        foreach ($this->fixers as $i => $fixer) {
            if ($fixer instanceof TitleCase) {
                $insertIndex = $i;
                break;
            }
        }

        $fixers = $this->fixers;
        array_splice($fixers, $insertIndex, 0, $preset->fixers());

        return new self($fixers);
    }

    public static function defaults(bool $lazy = true): self
    {
        return new self([
            new StripInvisible,
            new NormalizeApostrophe,
            new StripTrailingPunctuation,
            new SquishWhitespace,
            new HyphenSpacing,
            new TitleCase($lazy),
        ]);
    }
}
