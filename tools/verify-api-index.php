#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$generated = __DIR__.'/../docs/02-user-guide/12-full-api-index.md';
$command = 'php '.escapeshellarg(__DIR__.'/generate-api-index.php');
exec($command, $output, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, implode(PHP_EOL, $output).PHP_EOL);
    exit($exitCode);
}

$temp = $generated.'.tmp';
copy($generated, $temp);
exec($command);
$current = (string) file_get_contents($generated);
$expected = (string) file_get_contents($temp);
@unlink($temp);

if ($current !== $expected) {
    fwrite(STDERR, 'API index is out of date. Run php tools/generate-api-index.php'.PHP_EOL);
    exit(1);
}

echo 'API index is up to date.'.PHP_EOL;
