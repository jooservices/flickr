<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$file = $argv[1] ?? 'coverage.xml';
$threshold = (float) ($argv[2] ?? 85.00);

if (is_file($file) === false) {
    fwrite(STDERR, "Coverage file {$file} not found.\n");
    exit(1);
}

$xml = simplexml_load_file($file);

if ($xml === false || !isset($xml->project->metrics)) {
    fwrite(STDERR, "Coverage file {$file} is not a valid Clover report.\n");
    exit(1);
}

$metrics = $xml->project->metrics;
$elements = (int) $metrics['elements'];
$covered = (int) $metrics['coveredelements'];

if ($elements === 0) {
    fwrite(STDERR, "Coverage report has no elements.\n");
    exit(1);
}

$percentage = round($covered / $elements * 100, 2);

printf("Statements coverage: %.2f%% (%d/%d)\n", $percentage, $covered, $elements);

if ($percentage < $threshold) {
    printf("Coverage %.2f%% is below the required %.2f%% gate.\n", $percentage, $threshold);
    exit(1);
}

echo "Coverage gate passed.\n";
