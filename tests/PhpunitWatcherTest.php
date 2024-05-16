<?php

namespace Spatie\PhpUnitWatcher\Test;

use PHPUnit\Framework\TestCase;
use Spatie\PhpUnitWatcher\OS;
use Symfony\Component\Process\Process;
use PHPUnit\Framework\Attributes\Test;

class PhpunitWatcherTest extends TestCase
{
    #[Test]
    public function the_watcher_can_be_executed()
    {
        $process = new Process(OS::isOnWindows() ? ['php', 'phpunit-watcher'] : ['./phpunit-watcher']);

        $process->run();

        $this->assertTrue($process->isSuccessful());
    }
}
