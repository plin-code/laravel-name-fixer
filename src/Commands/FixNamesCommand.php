<?php

namespace PlinCode\LaravelNameFixer\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PlinCode\LaravelNameFixer\NameFixer;

class FixNamesCommand extends Command
{
    protected $signature = 'name-fixer:scan
        {model : The Eloquent model class}
        {--column=* : The columns holding names}
        {--locale= : A locale preset, for example "it"}
        {--chunk=500 : Rows read per query}
        {--fix : Save the changes instead of only listing them}';

    protected $description = 'Find and fix badly formatted names already stored in the database';

    public function handle(NameFixer $fixer): int
    {
        $class = $this->argument('model');
        $columns = $this->option('column');

        if (! is_subclass_of($class, Model::class)) {
            $this->error("{$class} is not an Eloquent model.");

            return self::FAILURE;
        }

        if ($columns === []) {
            $this->error('Pass at least one --column.');

            return self::FAILURE;
        }

        if ($this->option('locale')) {
            $fixer = $fixer->locale($this->option('locale'));
        }

        /** @var Model $model */
        $model = new $class;
        $keyName = $model->getKeyName();
        $query = $model->newQuery()->toBase();
        $changes = [];

        // The base query skips casts, events and timestamps: this is a data cleanup, not a user edit.
        $query->clone()
            ->select(array_unique([$keyName, ...$columns]))
            ->lazyById((int) $this->option('chunk'), $keyName)
            ->each(function (object $row) use ($fixer, $columns, $keyName, $query, &$changes) {
                $dirty = [];

                foreach ($columns as $column) {
                    $value = $row->{$column};

                    if (! is_string($value) || ($fixed = $fixer->fix($value)) === $value) {
                        continue;
                    }

                    $dirty[$column] = $fixed;
                    $changes[] = [$row->{$keyName}, $column, $value, $fixed];
                }

                if ($dirty !== [] && $this->option('fix')) {
                    $query->clone()->where($keyName, $row->{$keyName})->update($dirty);
                }
            });

        if ($changes === []) {
            $this->info('No names to fix.');

            return self::SUCCESS;
        }

        $this->table([$keyName, 'column', 'before', 'after'], $changes);

        $count = count($changes).' '.Str::plural('change', count($changes));

        if ($this->option('fix')) {
            $this->info("{$count} saved.");
        } else {
            $this->info("{$count} found. Run again with --fix to save them.");
        }

        return self::SUCCESS;
    }
}
