<?php

declare(strict_types=1);

$argv = [...($argv ?? []), '--check'];
require __DIR__ . '/generate-api-surface.php';
