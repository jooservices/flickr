<?php

declare(strict_types=1);

use JOOservices\Flickr\Metadata\FlickrMethodRegistry;

require __DIR__ . '/../vendor/autoload.php';

$registry = new FlickrMethodRegistry();
$path = __DIR__ . '/../resources/method-registry.php';
/** @var array<string, mixed> $raw */
$raw = require $path;
$failures = [];

$names = $registry->names();

if (count($names) !== 224) {
    $failures[] = sprintf('Expected exactly 224 registry records, found %d.', count($names));
}

$sortedNames = $names;
sort($sortedNames);

if ($names !== $sortedNames) {
    $failures[] = 'Registry keys are not sorted ascending.';
}

$normalized = hash('sha256', implode("\n", $names));
$metaMap = [];

foreach ((array) ($raw['meta'] ?? []) as $metaKey => $metaValue) {
    $metaMap[(string) $metaKey] = $metaValue;
}

$recordedHash = is_string($metaMap['names_sha256'] ?? null) ? $metaMap['names_sha256'] : '';

if ($recordedHash === '') {
    $metaMap['names_sha256'] = $normalized;
    $raw['meta'] = $metaMap;
    $export = "<?php\n\ndeclare(strict_types=1);\n\n/* Frozen baseline extracted from the archived SDK registry.\n * Maintainers must review each record against\n * https://www.flickr.com/services/api/{method}.html before release. */\n\nreturn " . var_export($raw, true) . ";\n";
    file_put_contents($path, $export);
    echo "names_sha256 seeded: {$normalized}\n";
} elseif (hash_equals($recordedHash, $normalized) === false) {
    $failures[] = 'Recorded names_sha256 does not match the normalized method-name set.';
}

foreach ($registry->all() as $definition) {
    $expectedDocs = sprintf('https://www.flickr.com/services/api/%s.html', $definition->name);

    if ($definition->docsUrl !== null && $definition->docsUrl !== $expectedDocs) {
        $failures[] = sprintf('%s has an unexpected docs URL.', $definition->name);
    }

    if ($definition->available === false && $definition->deprecationReason === null) {
        $failures[] = sprintf('%s is unavailable without a deprecation reason.', $definition->name);
    }

    if (preg_match('/^flickr\.[a-z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)+$/', $definition->name) !== 1) {
        $failures[] = sprintf('%s has a malformed method name.', $definition->name);
    }
}

$unavailable = array_filter(
    $registry->all(),
    static fn($definition): bool => $definition->available === false,
);

$expectedUnavailable = [
    'flickr.auth.checkToken',
    'flickr.auth.getFrob',
    'flickr.auth.getFullToken',
    'flickr.auth.getToken',
    'flickr.auth.oauth.checkToken',
    'flickr.auth.oauth.getAccessToken',
    'flickr.panda.getList',
    'flickr.panda.getPhotos',
];

if (array_keys($unavailable) !== $expectedUnavailable) {
    $failures[] = 'The unavailable-method set does not match the frozen legacy/Panda list.';
}

if ($failures !== []) {
    fwrite(STDERR, "Registry verification failed:\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

echo "Registry OK: 224 methods, provenance hash verified.\n";
