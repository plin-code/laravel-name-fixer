<?php

namespace PlinCode\LaravelNameFixer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use PlinCode\LaravelNameFixer\NameFixerServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            NameFixerServiceProvider::class,
        ];
    }
}
