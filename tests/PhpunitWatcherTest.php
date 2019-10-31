<?php

namespace Spatie\PhpUnitWatcher\Test;

use Spatie\PhpUnitWatcher\OS;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PhpunitWatcherTest extends TestCase
{
    /** @test */
    public function the_watcher_can_be_executed()
    {
        $process = new Process(OS::isOnWindows() ? ['php', 'phpunit-watcher'] : ['./phpunit-watcher']);

        $process->run();

        $this->assertTrue($process->isSuccessful());
    }
}
