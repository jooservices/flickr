<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$indexFile = $root . '/docs/api-index.md';

$before = @file_get_contents($indexFile);

if ($before === false) {
    fwrite(STDERR, "docs/api-index.md is missing. Run: composer generate:api-index\n");
    exit(1);
}

require __DIR__ . '/generate-api-surface.php';

$after = (string) file_get_contents($indexFile);

if (hash_equals($before, $after) === false) {
    fwrite(STDERR, "docs/api-index.md is stale. Run: composer generate:api-index\n");
    exit(1);
}

echo 'API index OK: ' . substr_count($after, '` → `') . " wrapper entries verified.\n";
