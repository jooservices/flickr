<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Integration;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class GeneratedSurfaceTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $root = dirname(__DIR__, 2);
        $this->directory = sys_get_temp_dir() . '/flickr-generator-' . Factory::create()->uuid();
        foreach (['tools', 'resources', 'src/Services', 'docs'] as $path) {
            self::assertTrue(mkdir($this->directory . '/' . $path, 0700, true));
        }
        self::assertTrue(symlink($root . '/vendor', $this->directory . '/vendor'));
        $files = [
            'tools/generate-api-surface.php', 'tools/verify-api-index.php',
            'resources/api-surface.php', 'src/Flickr.php', 'docs/api-index.md',
        ];
        $services = glob($root . '/src/Services/*.php');
        self::assertIsArray($services);
        foreach ($services as $file) {
            $files[] = 'src/Services/' . basename($file);
        }
        foreach ($files as $file) {
            self::assertTrue(copy($root . '/' . $file, $this->directory . '/' . $file));
        }
    }

    protected function tearDown(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($files as $file) {
            assert($file instanceof SplFileInfo);
            if ($file->isDir() && !$file->isLink()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($this->directory);
    }

    /** @return iterable<string, array{string}> */
    public static function generatedPaths(): iterable
    {
        yield 'facade' => ['src/Flickr.php'];
        yield 'service' => ['src/Services/ActivityApi.php'];
        yield 'index' => ['docs/api-index.md'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('generatedPaths')]
    public function testVerificationDetectsDriftWithoutRewritingIt(string $path): void
    {
        $target = $this->directory . '/' . $path;
        $content = (string) file_get_contents($target) . '\n' . Factory::create()->sentence();
        file_put_contents($target, $content);

        [$status, $output] = $this->verify();
        self::assertSame(1, $status, $output);
        self::assertSame($content, file_get_contents($target));
    }

    public function testVerificationAcceptsUnchangedGeneratedFiles(): void
    {
        [$status, $output] = $this->verify();
        self::assertSame(0, $status, $output);
    }

    /** @return array{int, string} */
    private function verify(): array
    {
        $process = proc_open(
            [PHP_BINARY, $this->directory . '/tools/verify-api-index.php'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
        );
        self::assertIsResource($process);
        $output = (string) stream_get_contents($pipes[1]) . (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $output];
    }
}
