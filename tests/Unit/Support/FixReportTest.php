<?php

use PlinCode\LaravelNameFixer\Support\FixReport;

test('exposes the report values', function () {
    $report = new FixReport(
        original: ' ROSSI ',
        fixed: 'Rossi',
        appliedFixers: ['squish_whitespace', 'title_case'],
        wasModified: true,
    );

    expect($report->original)->toBe(' ROSSI ')
        ->and($report->fixed)->toBe('Rossi')
        ->and($report->appliedFixers)->toBe(['squish_whitespace', 'title_case'])
        ->and($report->wasModified)->toBeTrue();
});
