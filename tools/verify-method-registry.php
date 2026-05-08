#!/usr/bin/env php
<?php

declare(strict_types=1);

use JOOservices\Flickr\Metadata\FlickrMethodDefinition;

require __DIR__.'/../vendor/autoload.php';

/** @var list<string> $official */
$official = require __DIR__.'/../tests/Fixtures/official-flickr-methods.php';

/** @var array<string, FlickrMethodDefinition> $registered */
$registered = require __DIR__.'/../src/Metadata/methods.php';

$errors = [];

foreach ($official as $method) {
    if (! isset($registered[$method])) {
        $errors[] = "Missing registry definition for {$method}.";

        continue;
    }

    $definition = $registered[$method];
    $expectedDocsUrl = 'https://www.flickr.com/services/api/'.$method.'.html';

    if ($definition->name !== $method) {
        $errors[] = "Registry key {$method} has mismatched definition name {$definition->name}.";
    }

    if ($definition->docsUrl !== $expectedDocsUrl) {
        $errors[] = "Registry definition {$method} has unexpected docs URL {$definition->docsUrl}.";
    }
}

foreach ($registered as $method => $definition) {
    if (! $definition instanceof FlickrMethodDefinition) {
        $errors[] = "Registry item {$method} is not a FlickrMethodDefinition.";

        continue;
    }

    if (! is_string($definition->docsUrl) || $definition->docsUrl === '') {
        $errors[] = "Registry definition {$method} is missing docs URL.";
    }
}

if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, $error.PHP_EOL);
    }

    exit(1);
}

echo 'Verified '.count($official).' official Flickr REST method definitions.'.PHP_EOL;
