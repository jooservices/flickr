<?php

declare(strict_types=1);

use JOOservices\Flickr\Metadata\FlickrMethodRegistry;

require __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);
$registry = new FlickrMethodRegistry();
/** @var array<string, mixed> $surface */
$surface = require $root . '/resources/api-surface.php';
$registryNames = $registry->names();
$remaining = array_flip($registryNames);

/**
 * @param mixed $value
 *
 * @return array<string, mixed>
 */
$mapOf = static function (mixed $value): array {
    $map = [];

    foreach ((array) $value as $key => $item) {
        $map[(string) $key] = $item;
    }

    return $map;
};

/**
 * @param mixed $value
 *
 * @return list<string>
 */
$listOfStrings = static function (mixed $value): array {
    $items = [];

    foreach ((array) $value as $item) {
        if (is_string($item)) {
            $items[] = $item;
        }
    }

    return $items;
};

$serviceSpecs = [];

foreach ($mapOf($surface['services'] ?? []) as $accessor => $spec) {
    $specMap = $mapOf($spec);
    $serviceSpecs[$accessor] = [
        'prefixes' => $listOfStrings($specMap['prefixes'] ?? []),
        'exclude_prefixes' => $listOfStrings($specMap['exclude_prefixes'] ?? []),
        'exclude_methods' => $listOfStrings($specMap['exclude_methods'] ?? []),
        'methods' => $listOfStrings($specMap['methods'] ?? []),
        'hand_written' => ($specMap['hand_written'] ?? false) === true,
        'facade_accessor' => ($specMap['facade_accessor'] ?? true) !== false,
    ];
}

$services = [];
$coverage = [];

foreach ($serviceSpecs as $accessor => $spec) {
    $class = ucfirst($accessor) . 'Api';
    $methods = [];

    foreach ($registryNames as $name) {
        $matched = false;

        foreach ($spec['prefixes'] as $prefix) {
            if (str_starts_with($name, $prefix)) {
                $matched = true;
                break;
            }
        }

        if ($matched === false) {
            continue;
        }

        foreach ($spec['exclude_prefixes'] as $excluded) {
            if (str_starts_with($name, $excluded)) {
                $matched = false;
                break;
            }
        }

        if ($matched && in_array($name, $spec['exclude_methods'], true)) {
            $matched = false;
        }

        if ($matched) {
            $methods[] = $name;
            $coverage[] = $name;
            unset($remaining[$name]);
        }
    }

    foreach ($spec['methods'] as $explicit) {
        if ($registry->find($explicit) !== null) {
            $methods[] = $explicit;
            $coverage[] = $explicit;
            unset($remaining[$explicit]);
        }
    }

    sort($methods);

    $wrappers = [];

    foreach ($methods as $method) {
        $wrapper = substr($method, (int) strrpos($method, '.') + 1);

        if (isset($wrappers[$wrapper])) {
            $segments = explode('.', $method);
            $count = count($segments);
            $wrapper = $segments[$count - 2] . ucfirst($segments[$count - 1]);
        }

        if (isset($wrappers[$wrapper])) {
            fwrite(STDERR, sprintf("Unresolvable wrapper collision in %s: %s\n", $class, $wrapper));
            exit(1);
        }

        $wrappers[$wrapper] = $method;
    }

    $services[$accessor] = [
        'class' => $class,
        'wrappers' => $wrappers,
        'hand_written' => $spec['hand_written'],
        'facade_accessor' => $spec['facade_accessor'],
    ];
}

if ($coverage !== $registryNames) {
    sort($coverage);

    if ($coverage !== $registryNames) {
        fwrite(STDERR, sprintf("API surface does not cover the registry exactly (%d vs %d).\n", count($coverage), count($registryNames)));
        exit(1);
    }
}

/**
 * @param array<string, string> $wrappers
 */
function emitApiService(string $root, string $accessor, string $class, array $wrappers): void
{
    $lines = [
        '<?php',
        '',
        'declare(strict_types=1);',
        '',
        'namespace JOOservices\Flickr\Services;',
        '',
        'use JOOservices\Flickr\Api\ApiCallOptions;',
        'use JOOservices\Flickr\Dtos\Common\ApiResponseData;',
        '',
        '/** Generated from resources/api-surface.php. Do not edit by hand. */',
        "final class {$class} extends AbstractApiService",
        '{',
    ];

    foreach ($wrappers as $wrapper => $method) {
        $lines[] = '    /**';
        $lines[] = '     * @param array<string, mixed> $parameters';
        $lines[] = '     */';
        $lines[] = '    public function ' . $wrapper . '(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData';
        $lines[] = '    {';
        $lines[] = '        return $this->call(\'' . $method . '\', $parameters, $options);';
        $lines[] = '    }';
        $lines[] = '';
    }

    if ($wrappers !== []) {
        array_pop($lines);
    }

    $lines[] = '}';
    $lines[] = '';

    file_put_contents($root . '/src/Services/' . $class . '.php', implode("\n", $lines));
}

foreach ($services as $accessor => $service) {
    if ($service['hand_written'] === false) {
        emitApiService($root, $accessor, $service['class'], $service['wrappers']);
    }
}

$mapLines = [];
$accessorLines = [];

foreach ($services as $accessor => $service) {
    $onFacade = $service['facade_accessor'];

    if ($onFacade) {
        $mapLines[] = sprintf(
            "        '%s' => '%s',",
            $accessor,
            '\\JOOservices\\Flickr\\Services\\' . $service['class'],
        );
        $accessorLines[] = sprintf(
            "    public function %s(): \\JOOservices\\Flickr\\Services\\%s\n    {\n"
            . "        /** @var %s \$instance */\n"
            . "        \$instance = \$this->resolved['%s'] ??= new %s(\$this->api);\n\n"
            . "        return \$instance;\n    }",
            $accessor,
            $service['class'],
            $service['class'],
            $accessor,
            $service['class'],
        );
    }
}

$serviceImports = [];

foreach ($services as $service) {
    if ($service['facade_accessor']) {
        $serviceImports[] = 'use JOOservices\Flickr\Services\\' . $service['class'] . ';';
    }
}

sort($serviceImports);

$flickr = [
    '<?php',
    '',
    'declare(strict_types=1);',
    '',
    'namespace JOOservices\Flickr;',
    '',
    'use JOOservices\Flickr\Api\Api;',
    'use JOOservices\Flickr\Auth\OAuth1Authenticator;',
    ...$serviceImports,
    'use JOOservices\Flickr\Upload\UploadService;',
    '',
    '/** Generated facade: explicit universal gateway plus domain accessors. No magic dispatch. */',
    'final class Flickr',
    '{',
    '    /** @var array<string, object> */',
    '    private array $resolved = [];',
    '',
    '    public function __construct(',
    '        private readonly Api $api,',
    '        private readonly OAuth1Authenticator $oauthAuthenticator,',
    '        private readonly UploadService $uploadsService,',
    '    ) {',
    '    }',
    '',
    '    public function api(): Api',
    '    {',
    '        return $this->api;',
    '    }',
    '',
    '    public function oauth(): OAuth1Authenticator',
    '    {',
    '        return $this->oauthAuthenticator;',
    '    }',
    '',
    '    public function uploads(): UploadService',
    '    {',
    '        return $this->uploadsService;',
    '    }',
    '',
    ...$accessorLines,
    '}',
    '',
];

file_put_contents($root . '/src/Flickr.php', implode("\n", $flickr));

$index = ["# Flickr SDK v4 API index", '', 'Generated from resources/method-registry.php + resources/api-surface.php.', ''];

foreach ($services as $accessor => $service) {
    $index[] = '## `' . $accessor . '()` → ' . $service['class'];
    $index[] = '';

    foreach ($service['wrappers'] as $wrapper => $method) {
        $definition = $registry->find($method);
        $flags = [
            $definition !== null ? $definition->httpMethod->value : 'GET',
            $definition !== null ? $definition->permission->value : 'none',
        ];

        if ($definition !== null && $definition->available === false) {
            $flags[] = 'unavailable';
        }

        $index[] = sprintf('- `%s()` → `%s` [%s]', $wrapper, $method, implode(', ', $flags));
    }

    $index[] = '';
}

@mkdir($root . '/docs', 0777, true);
file_put_contents($root . '/docs/api-index.md', implode("\n", $index));

echo sprintf(
    "Generated %d generated service classes (+%d hand-written), facade, api-index for %d methods.\n",
    count(array_filter($services, static fn($s): bool => $s['hand_written'] === false)),
    count(array_filter($services, static fn($s): bool => $s['hand_written'] === true)),
    count($registryNames),
);
