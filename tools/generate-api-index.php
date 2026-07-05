#!/usr/bin/env php
<?php

declare(strict_types=1);

use JOOservices\Flickr\Metadata\FlickrMethodDefinition;

require __DIR__.'/../vendor/autoload.php';

/** @var array<string, FlickrMethodDefinition> $registered */
$registered = require __DIR__.'/../src/Metadata/methods.php';

$lines = [
    '# Full API Index',
    '',
    'Generated from `src/Metadata/methods.php`. Do not edit manually.',
    '',
    '| Method | Auth | Permission | HTTP | Cacheable | Deprecated | Docs |',
    '| --- | --- | --- | --- | --- | --- | --- |',
];

ksort($registered);

foreach ($registered as $definition) {
    $lines[] = sprintf(
        '| `%s` | %s | %s | %s | %s | %s | [%s](%s) |',
        $definition->name,
        $definition->requiresAuth ? 'yes' : 'no',
        $definition->authPermission?->value ?? '-',
        $definition->httpMethod->value,
        $definition->cacheable ? 'yes' : 'no',
        $definition->deprecated ? 'yes' : 'no',
        $definition->name,
        $definition->docsUrl ?? '#',
    );
}

$target = __DIR__.'/../docs/02-user-guide/12-full-api-index.md';
$content = implode(PHP_EOL, $lines).PHP_EOL;

if (! is_dir(dirname($target))) {
    mkdir(dirname($target), 0777, true);
}

file_put_contents($target, $content);
echo 'Generated '.$target.' with '.count($registered).' methods.'.PHP_EOL;
