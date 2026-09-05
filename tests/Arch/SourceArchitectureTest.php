<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Arch;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class SourceArchitectureTest extends TestCase
{
    private const SRC = __DIR__ . '/../../src/';

    /** @return list<string> absolute paths of all src php files */
    private function sourceFiles(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::SRC, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        $files = [];

        foreach ($iterator as $file) {
            assert($file instanceof \SplFileInfo);

            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    public function testEverySourceFileDeclaresStrictTypes(): void
    {
        self::assertGreaterThan(10, count($this->sourceFiles()));

        foreach ($this->sourceFiles() as $file) {
            $content = (string) file_get_contents($file);
            self::assertStringContainsString('declare(strict_types=1);', $content, $file . ' must declare strict_types.');
        }
    }

    public function testNoForbiddenDependenciesOrConstructs(): void
    {
        $forbidden = ['GuzzleHttp\\', 'Illuminate\\', 'Laravel\\', 'Symfony\\Component\\', '__call(', 'parse_str(', 'eval('];

        foreach ($this->sourceFiles() as $file) {
            $content = (string) file_get_contents($file);
            foreach ($forbidden as $needle) {
                self::assertStringNotContainsString($needle, $content, sprintf('%s must not contain %s', $file, $needle));
            }
        }
    }

    private const OPEN_CLASS_ALLOWLIST = [
        '/Exceptions/ApiException.php',
    ];

    public function testClassesAreFinalEnumsInterfacesOnly(): void
    {
        foreach ($this->sourceFiles() as $file) {
            foreach (self::OPEN_CLASS_ALLOWLIST as $openClassPath) {
                if (str_contains($file, $openClassPath)) {
                    continue 2;
                }
            }

            $content = (string) file_get_contents($file);

            if (preg_match('/^\s*(abstract|interface|enum)\b/m', $content) === 1) {
                continue;
            }

            if (preg_match('/^\s*(final\s+)?class\s+/m', $content) === 1) {
                self::assertMatchesRegularExpression('/^\s*final\s+class\s+/m', $content, $file . ' must be final.');
            }
        }
    }

    public function testFlickrHostLiteralsLiveOnlyInConfig(): void
    {
        foreach ($this->sourceFiles() as $file) {
            if (str_contains($file, '/Config/')) {
                continue;
            }

            $content = (string) file_get_contents($file);
            self::assertStringNotContainsString('://www.flickr.com', $content, $file . ' must not hardcode Flickr hosts.');
            self::assertStringNotContainsString('://up.flickr.com', $content, $file . ' must not hardcode Flickr upload hosts.');
        }
    }

    public function testReadmeImportsResolveAgainstInstalledDependencies(): void
    {
        $readme = (string) file_get_contents(__DIR__ . '/../../README.md');
        preg_match_all('/^use ([A-Za-z0-9_\\\\]+);$/m', $readme, $matches);
        self::assertNotEmpty($matches[1]);
        foreach ($matches[1] as $class) {
            self::assertTrue(class_exists($class), 'Unresolvable README import: ' . $class);
        }
    }
}
