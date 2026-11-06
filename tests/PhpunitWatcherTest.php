<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\OS;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class PhpunitWatcherTest extends TestCase
{
    #[Test]
    public function the_watcher_can_be_executed()
    {
        $process = new Process(OS::isOnWindows() ? ['php', 'phpunit-watcher'] : ['./phpunit-watcher']);

        $process->run();

        $this->assertTrue($process->isSuccessful());
    }

    #[Test]
    public function it_runs_the_project_tests_and_keeps_watching_after_a_failure(): void
    {
        $directory = sys_get_temp_dir().'/phpunit-watcher-'.bin2hex(random_bytes(8));
        $filesystem = new Filesystem();
        $filesystem->mkdir($directory.'/tests');
        $filesystem->dumpFile($directory.'/tests/expected.php', '<?php return 41;');
        $filesystem->dumpFile($directory.'/tests/ExampleTest.php', <<<'PHP'
            <?php

            use PHPUnit\Framework\TestCase;

            class ExampleTest extends TestCase
            {
                public function test_selected(): void
                {
                    $this->assertSame(42, require __DIR__.'/expected.php');
                }

                public function test_not_selected(): void
                {
                    $this->fail('The CLI filter was not forwarded.');
                }
            }
            PHP);
        $binary = realpath(__DIR__.'/../vendor/bin/phpunit');
        $filesystem->dumpFile($directory.'/.phpunit-watcher.yml', <<<YAML
            notifications:
              passingTests: false
              failingTests: false
            phpunit:
              binaryPath: '{$binary}'
              arguments: '--version'
            YAML);

        $process = new Process([
            PHP_BINARY,
            __DIR__.'/../phpunit-watcher',
            'watch',
            '--no-configuration',
            '--colors=never',
            '--filter=test_selected',
            'tests/ExampleTest.php',
        ], $directory);
        $process->setTimeout(15);

        try {
            $process->start();
            $this->assertTrue($process->waitUntil(fn () => str_contains($process->getOutput(), 'FAILURES!')), $process->getOutput().$process->getErrorOutput());
            $this->assertStringContainsString('Tests: 1, Assertions: 1, Failures: 1.', $process->getOutput());
            $this->assertTrue($process->isRunning());

            usleep(500000);
            $filesystem->dumpFile($directory.'/tests/expected.php', '<?php return 42;');

            $this->assertTrue($process->waitUntil(fn () => str_contains($process->getOutput(), 'OK (1 test, 1 assertion)')), $process->getOutput().$process->getErrorOutput());
            $this->assertStringNotContainsString('The CLI filter was not forwarded.', $process->getOutput());
            $this->assertTrue($process->isRunning());
        } finally {
            $process->stop();
            $filesystem->remove($directory);
        }
    }
}
