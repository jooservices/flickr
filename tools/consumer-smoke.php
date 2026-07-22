#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Consumer smoke test: require the package as a consumer would and exercise a
 * minimal public API surface without calling Flickr.
 */
$workspace = dirname(__DIR__);
$scratch = sys_get_temp_dir().'/jooservices-flickr-smoke-'.bin2hex(random_bytes(4));
mkdir($scratch);

$autoload = $workspace.'/vendor/autoload.php';
if (! is_file($autoload)) {
    fwrite(STDERR, "Run composer install in the package root first.\n");
    exit(1);
}

$composerJson = [
    'name' => 'jooservices/flickr-smoke',
    'require' => [
        'php' => '>=8.5',
        'jooservices/flickr' => '*',
    ],
    'repositories' => [
        [
            'type' => 'path',
            'url' => $workspace,
            'options' => ['symlink' => false],
        ],
    ],
    'minimum-stability' => 'dev',
    'prefer-stable' => true,
];

file_put_contents($scratch.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

$cmd = 'composer install --no-interaction --working-dir='.escapeshellarg($scratch);
passthru($cmd, $code);
if ($code !== 0) {
    fwrite(STDERR, "Smoke composer install failed.\n");
    exit($code);
}

$script = <<<'PHP'
<?php
require __DIR__ . '/vendor/autoload.php';

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Testing\FlickrFake;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;

$fake = FlickrFake::create(new FlickrConfig('k', 's', enableCircuitBreaker: false, enableRateLimit: false));
$fake->respond('flickr.photos.search', [
    'photos' => ['page' => 1, 'pages' => 1, 'perpage' => 1, 'total' => 0, 'photo' => []],
]);
$response = $fake->flickr()->photos()->search(SearchPhotosData::from(['text' => 'cat', 'perPage' => 1]));
$info = $fake->flickr()->describe('flickr.photos.search');
if (! $response->ok || $info === null) {
    fwrite(STDERR, "Smoke assertions failed.\n");
    exit(1);
}
echo "smoke-ok\n";
PHP;

file_put_contents($scratch.'/smoke.php', $script);
passthru('php '.escapeshellarg($scratch.'/smoke.php'), $smokeCode);

// Best-effort cleanup
passthru('rm -rf '.escapeshellarg($scratch));

exit($smokeCode);
